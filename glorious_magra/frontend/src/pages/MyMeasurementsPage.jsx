import { useEffect, useState } from 'react';
import { measurementApi } from '../services/api';
import { useAuthStore } from '../stores/authStore';
import { useI18n } from '../i18n/useI18n';

function MyMeasurementsPage() {
  const token = useAuthStore((state) => state.token);
  const { t } = useI18n();
  const [measurements, setMeasurements] = useState([]);
  const [error, setError] = useState('');

  useEffect(() => {
    const loadMeasurements = async () => {
      try {
        const response = await measurementApi.list(token);
        setMeasurements(response.data || []);
      } catch (err) {
        setError(err.message || t('measurements.loadError'));
      }
    };

    if (token) {
      loadMeasurements();
    }
  }, [token, t]);

  return (
    <div className="grid">
      <section className="hero">
        <h1>{t('measurements.title')}</h1>
        <p className="muted">{t('measurements.subtitle')}</p>
      </section>

      <section className="card">
        <h2>{t('measurements.listTitle')}</h2>
        {error ? <p className="error">{error}</p> : null}
        {!error && measurements.length === 0 ? (
          <p className="muted">{t('measurements.empty')}</p>
        ) : null}
        {!error && measurements.length > 0 ? (
          <div className="grid dashboard-list">
            {measurements.map((measurement) => (
              <div key={measurement.id} className="stat">
                <div><strong>{measurement.date}</strong></div>
                <div>{t('dashboard.fatLabel')}: {measurement.calculatedFatPercentage}</div>
                <div>{t('dashboard.weight')}: {measurement.weight} kg</div>
                <div>{t('dashboard.waistShort')}: {measurement.waist} cm</div>
              </div>
            ))}
          </div>
        ) : null}
      </section>
    </div>
  );
}

export default MyMeasurementsPage;
