import { useEffect, useMemo, useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { measurementApi } from '../services/api';
import { useAuthStore } from '../stores/authStore';
import { useI18n } from '../i18n/useI18n';

const createEmptyForm = () => ({
  date: new Date().toISOString().slice(0, 10),
  weight: '',
  height: '',
  neck: '',
  waist: '',
  hip: '',
});

const createPrefilledForm = (measurement = {}) => ({
  ...createEmptyForm(),
  weight: measurement.weight !== undefined ? String(measurement.weight) : '',
  height: measurement.height !== undefined ? String(measurement.height) : '',
  neck: measurement.neck !== undefined ? String(measurement.neck) : '',
  waist: measurement.waist !== undefined ? String(measurement.waist) : '',
  hip: measurement.hip !== undefined && measurement.hip !== null ? String(measurement.hip) : '',
});

function AddMeasurementPage() {
  const token = useAuthStore((state) => state.token);
  const user = useAuthStore((state) => state.user);
  const { t } = useI18n();
  const navigate = useNavigate();

  const [measurements, setMeasurements] = useState([]);
  const [form, setForm] = useState(createEmptyForm);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [loading, setLoading] = useState(false);

  const latestMeasurement = useMemo(() => measurements[0] || null, [measurements]);

  useEffect(() => {
    const loadMeasurements = async () => {
      try {
        const response = await measurementApi.list(token);
        const list = response.data || [];
        setMeasurements(list);
        setForm(createPrefilledForm(list[0] || {}));
      } catch (err) {
        setError(err.message || t('dashboard.loadError'));
      }
    };

    if (token) {
      loadMeasurements();
    }
  }, [token, t]);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setLoading(true);
    setError('');
    setSuccess('');

    try {
      const payload = {
        date: form.date,
        weight: Number(form.weight),
        height: Number(form.height),
        neck: Number(form.neck),
        waist: Number(form.waist),
        hip: user?.biologicalSex === 'male' || !form.hip ? undefined : Number(form.hip),
      };

      await measurementApi.create(token, payload);
      setSuccess(t('dashboard.measurementSaved'));
      setForm(createPrefilledForm(payload));
      navigate('/');
    } catch (err) {
      setError(err.message || t('dashboard.saveError'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="grid">
      <section className="hero">
        <h1>{t('dashboard.logMeasurement')}</h1>
        <p className="muted">{t('dashboard.measurementReminder')}</p>
      </section>

      <section className="card">
        <form className="form-grid" onSubmit={handleSubmit}>
          <div className="field">
            <label>{t('common.date')}</label>
            <input type="date" value={form.date} onChange={(event) => setForm({ ...form, date: event.target.value })} required />
          </div>
          <div className="measurement-grid">
            <div className="field">
              <label>{t('dashboard.weightLabel')}</label>
              <input type="number" step="0.1" value={form.weight} onChange={(event) => setForm({ ...form, weight: event.target.value })} required />
            </div>
            <div className="field">
              <label>{t('dashboard.heightLabel')}</label>
              <input type="number" step="0.1" value={form.height} onChange={(event) => setForm({ ...form, height: event.target.value })} required />
            </div>
            <div className="field">
              <label>{t('dashboard.neckLabel')}</label>
              <input type="number" step="0.1" value={form.neck} onChange={(event) => setForm({ ...form, neck: event.target.value })} required />
            </div>
            <div className="field">
              <label>{t('dashboard.waistLabel')}</label>
              <input type="number" step="0.1" value={form.waist} onChange={(event) => setForm({ ...form, waist: event.target.value })} required />
            </div>
            {user?.biologicalSex !== 'male' ? (
              <div className="field">
                <label>{t('dashboard.hipLabel')}</label>
                <input type="number" step="0.1" value={form.hip} onChange={(event) => setForm({ ...form, hip: event.target.value })} required={user?.biologicalSex === 'female'} />
              </div>
            ) : null}
          </div>

          {error ? <p className="error">{error}</p> : null}
          {success ? <p className="success">{success}</p> : null}

          <div className="dashboard-action-row">
            <button className="primary" type="submit" disabled={loading}>
              {loading ? t('dashboard.savingMeasurement') : t('dashboard.saveMeasurement')}
            </button>
            <Link className="secondary" to="/">
              {t('nav.progress')}
            </Link>
          </div>
        </form>
        {latestMeasurement ? null : <p className="muted">{t('dashboard.quickViewEmpty')}</p>}
      </section>
    </div>
  );
}

export default AddMeasurementPage;
