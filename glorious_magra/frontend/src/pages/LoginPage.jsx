import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { authApi } from '../services/api';
import { useAuthStore } from '../stores/authStore';
import { useI18n } from '../i18n/useI18n';

function LoginPage() {
  const navigate = useNavigate();
  const login = useAuthStore((state) => state.login);
  const { t } = useI18n();
  const [form, setForm] = useState({ email: '', password: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setLoading(true);
    setError('');

    try {
      const response = await authApi.login(form);
      login(response.data.user, response.data.token, response.data.refreshToken);
      localStorage.setItem('gloriousmagra.token', response.data.token);
      localStorage.setItem('gloriousmagra.user', JSON.stringify(response.data.user));
      localStorage.setItem('gloriousmagra.refreshToken', response.data.refreshToken);
      navigate('/');
    } catch (err) {
      setError(err.message || t('login.error'));
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
        <h1>{t('login.title')}</h1>
        <p className="muted">{t('login.subtitle')}</p>
      </div>
      <form className="form-grid" onSubmit={handleSubmit}>
        <div className="field">
          <label>{t('common.email')}</label>
          <input type="email" value={form.email} onChange={(event) => setForm({ ...form, email: event.target.value })} required />
        </div>
        <div className="field">
          <label>{t('common.password')}</label>
          <input type="password" value={form.password} onChange={(event) => setForm({ ...form, password: event.target.value })} required />
        </div>
        {error ? <p className="error">{error}</p> : null}
        <button className="primary" type="submit" disabled={loading}>
          {loading ? t('login.submitting') : t('login.submit')}
        </button>
      </form>
      <p className="muted" style={{ marginTop: '1rem' }}>
        {t('login.noAccount')} <Link to="/register">{t('login.createOne')}</Link>
      </p>
      </div>
    </div>
  );
}

export default LoginPage;
