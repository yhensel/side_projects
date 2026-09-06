import { useAuthStore } from '../stores/authStore';
import { useI18n } from '../i18n/useI18n';
import { usePreferencesStore } from '../stores/preferencesStore';

function ProfilePage() {
  const user = useAuthStore((state) => state.user);
  const { t, language } = useI18n();
  const setLanguage = usePreferencesStore((state) => state.setLanguage);

  return (
    <div className="grid profile-page-grid">
      <section className="hero">
        <div className="profile-title-row">
          <span className="profile-gear-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24" className="profile-gear-icon">
              <path
                d="M19.4 13.5a7.7 7.7 0 0 0 .1-1.5 7.7 7.7 0 0 0-.1-1.5l2-1.6a.6.6 0 0 0 .1-.8l-1.9-3.3a.6.6 0 0 0-.7-.2l-2.4 1a7.2 7.2 0 0 0-2.6-1.5l-.4-2.5a.6.6 0 0 0-.6-.5h-3.8a.6.6 0 0 0-.6.5L8 4.1a7.2 7.2 0 0 0-2.6 1.5l-2.4-1a.6.6 0 0 0-.7.2L.4 8.1a.6.6 0 0 0 .1.8l2 1.6a7.7 7.7 0 0 0-.1 1.5 7.7 7.7 0 0 0 .1 1.5l-2 1.6a.6.6 0 0 0-.1.8l1.9 3.3a.6.6 0 0 0 .7.2l2.4-1a7.2 7.2 0 0 0 2.6 1.5l.4 2.5a.6.6 0 0 0 .6.5h3.8a.6.6 0 0 0 .6-.5l.4-2.5a7.2 7.2 0 0 0 2.6-1.5l2.4 1a.6.6 0 0 0 .7-.2l1.9-3.3a.6.6 0 0 0-.1-.8l-2-1.6Zm-7.4 1.8a3.3 3.3 0 1 1 0-6.6 3.3 3.3 0 0 1 0 6.6Z"
                fill="currentColor"
              />
            </svg>
          </span>
          <h1>{t('profile.title')}</h1>
        </div>
        <p className="muted">{t('profile.subtitle')}</p>
      </section>

      <section className="card profile-settings">
        <h2>{t('profile.accountTitle')}</h2>
        <div className="profile-info-grid">
          <div className="field">
            <label>{t('common.firstName')}</label>
            <input value={user?.firstName || ''} disabled />
          </div>
          <div className="field">
            <label>{t('common.email')}</label>
            <input value={user?.email || ''} disabled />
          </div>
        </div>
      </section>

      <section className="card profile-settings">
        <h2>{t('profile.preferencesTitle')}</h2>
        <p className="muted">{t('profile.preferencesSubtitle')}</p>
        <div className="field profile-language-field">
          <label htmlFor="language-select">{t('common.language')}</label>
          <select id="language-select" value={language} onChange={(event) => setLanguage(event.target.value)}>
            <option value="en">{t('common.english')}</option>
            <option value="es">{t('common.spanish')}</option>
          </select>
        </div>
      </section>
    </div>
  );
}

export default ProfilePage;
