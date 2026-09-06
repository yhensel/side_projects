import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { authApi } from '../services/api';
import { useI18n } from '../i18n/useI18n';

function RegisterPage() {
  const navigate = useNavigate();
  const { t } = useI18n();
  const [form, setForm] = useState({ firstName: '', email: '', password: '', birthDate: '', biologicalSex: 'male' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setLoading(true);
    setError('');

    try {
      await authApi.register(form);
      navigate('/login');
    } catch (err) {
      setError(err.message || t('register.error'));
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="auth-shell">
      <div className="auth-mark">
        <img className="auth-logo" src="/logo.svg" alt="GloriousMagra" />
        <p className="auth-wordmark"><span>GLORIUS</span>MAGRA</p>
      </div>
      <div className="card">
      <div className="hero">
        <h1>{t('register.title')}</h1>
        <p className="muted">{t('register.subtitle')}</p>
      </div>
      <form className="form-grid" onSubmit={handleSubmit}>
        <div className="field">
          <label>{t('common.firstName')}</label>
          <input value={form.firstName} onChange={(event) => setForm({ ...form, firstName: event.target.value })} required />
        </div>
        <div className="field">
          <label>{t('common.email')}</label>
          <input type="email" value={form.email} onChange={(event) => setForm({ ...form, email: event.target.value })} required />
        </div>
        <div className="field">
          <label>{t('common.password')}</label>
          <input type="password" value={form.password} onChange={(event) => setForm({ ...form, password: event.target.value })} required />
        </div>
        <div className="field">
          <label>{t('common.birthDate')}</label>
          <input type="date" value={form.birthDate} onChange={(event) => setForm({ ...form, birthDate: event.target.value })} required />
        </div>
        <div className="field">
          <label>{t('common.biologicalSex')}</label>
          <select value={form.biologicalSex} onChange={(event) => setForm({ ...form, biologicalSex: event.target.value })}>
            <option value="male">{t('common.male')}</option>
            <option value="female">{t('common.female')}</option>
          </select>
        </div>
        {error ? <p className="error">{error}</p> : null}
        <button className="primary" type="submit" disabled={loading}>
          {loading ? t('register.submitting') : t('register.submit')}
        </button>
      </form>
      <p className="muted" style={{ marginTop: '1rem' }}>
        {t('register.hasAccount')} <Link to="/login">{t('register.signIn')}</Link>
      </p>
      </div>
    </div>
  );
}

export default RegisterPage;
