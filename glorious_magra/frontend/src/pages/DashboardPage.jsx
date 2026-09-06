import { useEffect, useMemo, useState } from 'react';
import { measurementApi } from '../services/api';
import { useAuthStore } from '../stores/authStore';
import { useI18n } from '../i18n/useI18n';

const GAUGE_SIZE = 120;
const GAUGE_RADIUS = 42;
const GAUGE_CIRCUMFERENCE = 2 * Math.PI * GAUGE_RADIUS;
const GAUGE_PATH = 'M 18 60 A 42 42 0 0 1 102 60';

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

const formatNumber = (value, fractionDigits = 1) => {
  if (!Number.isFinite(value)) {
    return '—';
  }

  return value.toFixed(fractionDigits);
};

const formatShortDate = (value, language) => {
  if (!value) {
    return '—';
  }

  const date = new Date(value);

  if (Number.isNaN(date.getTime())) {
    return value;
  }

  return new Intl.DateTimeFormat(language, {
    month: 'short',
    day: 'numeric',
  }).format(date);
};

const getBodyFatCategory = (bodyFatPercentage, biologicalSex, t) => {
  if (!Number.isFinite(bodyFatPercentage)) {
    return {
      label: t('dashboard.statusUnknown'),
      tone: 'neutral',
    };
  }

  if (biologicalSex === 'female') {
    if (bodyFatPercentage < 14) {
      return { label: t('dashboard.statusEssentialFat'), tone: 'ok' };
    }
    if (bodyFatPercentage < 21) {
      return { label: t('dashboard.statusAthlete'), tone: 'ok' };
    }
    if (bodyFatPercentage < 25) {
      return { label: t('dashboard.statusFit'), tone: 'ok' };
    }
    if (bodyFatPercentage < 32) {
      return { label: t('dashboard.statusAverage'), tone: 'neutral' };
    }
    if (bodyFatPercentage < 39) {
      return { label: t('dashboard.statusOverweight'), tone: 'warn' };
    }

    return { label: t('dashboard.statusObese'), tone: 'danger' };
  }

  if (bodyFatPercentage < 6) {
    return { label: t('dashboard.statusEssentialFat'), tone: 'ok' };
  }
  if (bodyFatPercentage < 14) {
    return { label: t('dashboard.statusAthlete'), tone: 'ok' };
  }
  if (bodyFatPercentage < 18) {
    return { label: t('dashboard.statusFit'), tone: 'ok' };
  }
  if (bodyFatPercentage < 25) {
    return { label: t('dashboard.statusAverage'), tone: 'neutral' };
  }
  if (bodyFatPercentage < 30) {
    return { label: t('dashboard.statusOverweight'), tone: 'warn' };
  }

  return { label: t('dashboard.statusObese'), tone: 'danger' };
};

function DashboardPage() {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const { t, language } = useI18n();
  const [measurements, setMeasurements] = useState([]);
  const [error, setError] = useState('');
  const [activeGaugeKey, setActiveGaugeKey] = useState(null);

  const loadMeasurements = async () => {
    try {
      const response = await measurementApi.list(token);
      setMeasurements(response.data || []);
    } catch (err) {
      setError(err.message || t('dashboard.loadError'));
    }
  };

  useEffect(() => {
    if (token) {
      loadMeasurements();
    }
  }, [token]);

  const latestMeasurement = useMemo(() => measurements[0] || null, [measurements]);
  const previousMeasurement = useMemo(() => measurements[1] || null, [measurements]);

  const fatTrendChart = useMemo(() => {
    if (measurements.length < 2) {
      return null;
    }

    const values = measurements
      .slice()
      .reverse()
      .map((measurement) => Number(measurement.calculatedFatPercentage))
      .filter((value) => Number.isFinite(value));

    if (values.length < 2) {
      return null;
    }

    const min = Math.min(...values);
    const max = Math.max(...values);
    const range = max - min || 1;
    const width = 320;
    const height = 150;
    const padding = 18;
    const innerHeight = height - padding * 2;
    const innerWidth = width - padding * 2;

    const points = values.map((value, index) => {
      const x = padding + (index / Math.max(values.length - 1, 1)) * innerWidth;
      const y = height - padding - ((value - min) / range) * innerHeight;
      return `${x},${y}`;
    });

    return {
      width,
      height,
      points,
      min,
      max,
      path: `M ${points.join(' L ')}`,
      current: values[values.length - 1],
      previous: values[values.length - 2],
    };
  }, [measurements]);

  const quickViewMetrics = useMemo(() => {
    if (!latestMeasurement) {
      return [];
    }

    const metricDefinitions = [
      {
        key: 'calculatedFatPercentage',
        label: t('dashboard.bodyFatGaugeLabel'),
        unit: '%',
        decimals: 1,
      },
      {
        key: 'weight',
        label: t('dashboard.weightGaugeLabel'),
        unit: 'kg',
        decimals: 1,
      },
      {
        key: 'waist',
        label: t('dashboard.waistGaugeLabel'),
        unit: 'cm',
        decimals: 1,
      },
    ];

    return metricDefinitions.map((definition) => {
      const historicalValues = measurements
        .map((measurement) => Number(measurement[definition.key]))
        .filter((value) => Number.isFinite(value));
      const currentValue = Number(latestMeasurement[definition.key]);
      const previousValue = previousMeasurement ? Number(previousMeasurement[definition.key]) : Number.NaN;
      const minValue = historicalValues.length > 0 ? Math.min(...historicalValues) : currentValue;
      const maxValue = historicalValues.length > 0 ? Math.max(...historicalValues) : currentValue;
      const range = maxValue - minValue;
      const progress = range > 0 ? clamp(((currentValue - minValue) / range) * 100, 0, 100) : 50;
      const delta = Number.isFinite(previousValue) ? currentValue - previousValue : null;
      const isBodyFatMetric = definition.key === 'calculatedFatPercentage';
      const bodyFatCategory = isBodyFatMetric ? getBodyFatCategory(currentValue, user?.biologicalSex, t) : null;

      return {
        ...definition,
        currentValue,
        currentLabel: formatNumber(currentValue, definition.decimals),
        deltaLabel: delta === null ? t('dashboard.noPreviousMeasurement') : `${delta > 0 ? '+' : ''}${formatNumber(delta, definition.decimals)} ${definition.unit}`,
        rangeLabel: `${formatNumber(minValue, definition.decimals)} - ${formatNumber(maxValue, definition.decimals)} ${definition.unit}`,
        dateLabel: formatShortDate(latestMeasurement.date, language),
        progress,
        statusLabel: bodyFatCategory?.label || null,
        statusTone: bodyFatCategory?.tone || null,
      };
    });
  }, [language, latestMeasurement, measurements, previousMeasurement, t, user?.biologicalSex]);

  return (
    <div className="grid">
      <section className="card quick-view-card">
        <div className="dashboard-action-row">
          <div>
            <h2>{t('dashboard.quickViewTitle')}</h2>
            <p className="muted">{t('dashboard.quickViewSubtitle')}</p>
          </div>
          <div className="quick-view-badge muted">
            {measurements.length} {t('dashboard.entriesLabel')}
          </div>
        </div>
        {quickViewMetrics.length > 0 ? (
          <div className="gauge-grid">
            {quickViewMetrics.map((metric) => {
              const strokeDashoffset = 100 - metric.progress;
              const isInfoOpen = activeGaugeKey === metric.key;

              return (
                <article key={metric.key} className="gauge-card">
                  <div className="gauge-header">
                    <div>
                      <h3 className="gauge-title">{metric.label}</h3>
                      <span className="gauge-date">{metric.dateLabel}</span>
                    </div>
                  </div>
                  <button
                    className={`gauge-stage gauge-stage-button${isInfoOpen ? ' open' : ''}`}
                    type="button"
                    aria-label={t('dashboard.gaugeHelpTitle')}
                    aria-expanded={isInfoOpen}
                    onClick={() => setActiveGaugeKey(isInfoOpen ? null : metric.key)}
                  >
                    <svg viewBox="0 0 120 72" className="gauge-svg" aria-hidden="true">
                      <path className="gauge-track" d={GAUGE_PATH} pathLength="100" />
                      <path
                        className="gauge-fill"
                        d={GAUGE_PATH}
                        pathLength="100"
                        strokeDasharray="100"
                        strokeDashoffset={strokeDashoffset}
                      />
                    </svg>
                    <div className="gauge-readout">
                      <strong className="gauge-value">{metric.currentLabel}</strong>
                      <span className="gauge-unit">{metric.unit}</span>
                      {metric.statusLabel ? <span className={`gauge-status gauge-status-${metric.statusTone}`}>{metric.statusLabel}</span> : null}
                    </div>
                    <div className="gauge-info-overlay" aria-hidden={!isInfoOpen}>
                      {metric.statusLabel ? <span className={`gauge-info-line gauge-info-status gauge-status-${metric.statusTone}`}>{metric.statusLabel}</span> : null}
                      <span className="gauge-info-line">{t('dashboard.vsPreviousLabel')}: {metric.deltaLabel}</span>
                      <span className="gauge-info-line">{t('dashboard.recentRangeLabel')}: {metric.rangeLabel}</span>
                    </div>
                  </button>
                </article>
              );
            })}
          </div>
        ) : (
          <p className="muted">{t('dashboard.quickViewEmpty')}</p>
        )}
      </section>

      {fatTrendChart ? (
        <section className="card">
          <div className="progress-chart-wrapper">
            <div className="chart-legend-row">
              <h3>{t('dashboard.progressChartTitle')}</h3>
              <span className="muted">{t('dashboard.chartCurrent')}: {formatNumber(fatTrendChart.current, 1)}% / {t('dashboard.chartPrevious')}: {formatNumber(fatTrendChart.previous, 1)}%</span>
            </div>
            <svg className="progress-chart" viewBox={`0 0 ${fatTrendChart.width} ${fatTrendChart.height}`} role="img" aria-label={t('dashboard.progressChartTitle')}>
              <line className="chart-grid" x1="18" y1="132" x2="302" y2="132" />
              <line className="chart-grid" x1="18" y1="18" x2="302" y2="18" />
              <line className="chart-axis" x1="18" y1="18" x2="18" y2="132" />
              <line className="chart-axis" x1="18" y1="132" x2="302" y2="132" />
              <path className="chart-line" d={fatTrendChart.path} />
              {fatTrendChart.points.map((point, index) => {
                const [x, y] = point.split(',');
                return <circle key={`${point}-${index}`} className="chart-dot" cx={x} cy={y} r="4" />;
              })}
            </svg>
          </div>
        </section>
      ) : null}

      <section className="dashboard-dock" aria-label={t('dashboard.summaryDockLabel')}>
        <div className="dock-stat">
          <strong>{t('dashboard.latestFat')}</strong>
          <div>{latestMeasurement?.calculatedFatPercentage ?? '—'}</div>
        </div>
        <div className="dock-stat">
          <strong>{t('dashboard.weight')}</strong>
          <div>{latestMeasurement?.weight ?? '—'}</div>
        </div>
        <div className="dock-stat">
          <strong>{t('dashboard.height')}</strong>
          <div>{latestMeasurement?.height ?? '—'}</div>
        </div>
      </section>

    </div>
  );
}

export default DashboardPage;
