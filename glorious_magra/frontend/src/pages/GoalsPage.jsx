import { useEffect, useMemo, useState } from 'react';
import { measurementApi } from '../services/api';
import { useAuthStore } from '../stores/authStore';
import { useGoalStore } from '../stores/goalsStore';
import { useI18n } from '../i18n/useI18n';

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

function GoalsPage() {
  const token = useAuthStore((state) => state.token);
  const { t } = useI18n();
  const { goal, setGoal } = useGoalStore();
  const [measurements, setMeasurements] = useState([]);
  const [form, setForm] = useState({
    goalType: goal.goalType || 'fatLoss',
    targetWeight: goal.targetWeight ?? '',
    targetFatPercentage: goal.targetFatPercentage ?? '',
    targetDate: goal.targetDate ?? '',
  });
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    setForm({
      goalType: goal.goalType || 'fatLoss',
      targetWeight: goal.targetWeight ?? '',
      targetFatPercentage: goal.targetFatPercentage ?? '',
      targetDate: goal.targetDate ?? '',
    });
  }, [goal]);

  useEffect(() => {
    const loadMeasurements = async () => {
      try {
        const response = await measurementApi.list(token);
        setMeasurements(response.data || []);
      } catch (error) {
        setMeasurements([]);
      }
    };

    if (token) {
      loadMeasurements();
    }
  }, [token]);

  const latestMeasurement = measurements[0] || null;

  const goalSummary = useMemo(() => {
    if (!latestMeasurement) {
      return [];
    }

    const entries = [];
    const targetWeight = Number(form.targetWeight);
    const currentWeight = Number(latestMeasurement.weight);

    if (Number.isFinite(targetWeight) && Number.isFinite(currentWeight) && targetWeight > 0) {
      const diff = currentWeight - targetWeight;
      const progress = clamp(100 - (Math.abs(diff) / Math.max(Math.abs(currentWeight), 1)) * 100, 0, 100);
      entries.push({
        label: t('goals.weightTarget'),
        current: `${currentWeight} kg`,
        target: `${targetWeight} kg`,
        delta: `${diff > 0 ? '-' : '+'}${Math.abs(diff).toFixed(1)} kg`,
        progress,
      });
    }

    const targetFat = Number(form.targetFatPercentage);
    const currentFat = Number(latestMeasurement.calculatedFatPercentage);

    if (Number.isFinite(targetFat) && Number.isFinite(currentFat) && targetFat > 0) {
      const diff = currentFat - targetFat;
      const progress = clamp(100 - (Math.abs(diff) / Math.max(Math.abs(currentFat), 1)) * 100, 0, 100);
      entries.push({
        label: t('goals.fatTarget'),
        current: `${currentFat.toFixed(1)}%`,
        target: `${targetFat.toFixed(1)}%`,
        delta: `${diff > 0 ? '-' : '+'}${Math.abs(diff).toFixed(1)} %`,
        progress,
      });
    }

    return entries;
  }, [form.targetFatPercentage, form.targetWeight, latestMeasurement, t]);

  const handleSubmit = (event) => {
    event.preventDefault();
    setGoal(form);
    setSaved(true);
  };

  return (
    <div className="grid goals-page-grid">
      <section className="hero">
        <h1>{t('goals.title')}</h1>
        <p className="muted">{t('goals.subtitle')}</p>
      </section>

      <section className="card goals-card">
        <form onSubmit={handleSubmit} className="goal-form">
          <div className="field">
            <label htmlFor="goal-type">{t('goals.goalType')}</label>
            <select
              id="goal-type"
              value={form.goalType}
              onChange={(event) => setForm({ ...form, goalType: event.target.value })}
            >
              <option value="fatLoss">{t('goals.fatLoss')}</option>
              <option value="muscleGain">{t('goals.muscleGain')}</option>
            </select>
          </div>

          <div className="field">
            <label htmlFor="goal-weight">{t('goals.targetWeight')}</label>
            <input
              id="goal-weight"
              type="number"
              step="0.1"
              value={form.targetWeight}
              onChange={(event) => setForm({ ...form, targetWeight: event.target.value })}
              placeholder="70"
            />
          </div>

          <div className="field">
            <label htmlFor="goal-fat">{t('goals.targetFat')}</label>
            <input
              id="goal-fat"
              type="number"
              step="0.1"
              min="0"
              max="100"
              value={form.targetFatPercentage}
              onChange={(event) => setForm({ ...form, targetFatPercentage: event.target.value })}
              placeholder="18"
            />
          </div>

          <div className="field">
            <label htmlFor="goal-date">{t('goals.targetDate')}</label>
            <input
              id="goal-date"
              type="date"
              value={form.targetDate}
              onChange={(event) => setForm({ ...form, targetDate: event.target.value })}
            />
          </div>

          <button type="submit" className="primary-button">
            {t('goals.saveGoal')}
          </button>
        </form>

        {saved ? <p className="success-message">{t('goals.saved')}</p> : null}
      </section>

      <section className="card goal-summary-card">
        <h2>{t('goals.summary')}</h2>
        {!latestMeasurement ? (
          <p className="muted">{t('goals.waitingForMeasurement')}</p>
        ) : goalSummary.length === 0 ? (
          <p className="muted">{t('goals.noTargets')}</p>
        ) : (
          <div className="goal-summary-grid">
            {goalSummary.map((item) => (
              <article key={item.label} className="goal-summary-item">
                <div className="goal-summary-row">
                  <strong>{item.label}</strong>
                  <span>{item.delta}</span>
                </div>
                <div className="goal-progress-bar" aria-label={item.label}>
                  <span style={{ width: `${item.progress}%` }} />
                </div>
                <div className="goal-summary-metrics">
                  <span>{t('goals.current')}: {item.current}</span>
                  <span>{t('goals.target')}: {item.target}</span>
                </div>
              </article>
            ))}
          </div>
        )}
      </section>
    </div>
  );
}

export default GoalsPage;
