import { Link, NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom';
import { useAuthStore } from '../stores/authStore';
import { useI18n } from '../i18n/useI18n';

const MenuIcon = ({ children }) => (
  <svg viewBox="0 0 24 24" className="nav-menu-icon" aria-hidden="true">
    {children}
  </svg>
);

const ProgressIcon = () => (
  <MenuIcon>
    <path d="M4 19.5h16" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.8" />
    <path d="M6.5 16.5l3.9-4.3 2.7 2.3 4.4-6" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" />
    <circle cx="6.5" cy="16.5" r="1" fill="currentColor" />
    <circle cx="10.4" cy="12.2" r="1" fill="currentColor" />
    <circle cx="13.1" cy="14.5" r="1" fill="currentColor" />
    <circle cx="17.5" cy="8.5" r="1" fill="currentColor" />
  </MenuIcon>
);

const MeasurementsIcon = () => (
  <MenuIcon>
    <path d="M6 4.5h12a1.5 1.5 0 0 1 1.5 1.5v12A1.5 1.5 0 0 1 18 19.5H6A1.5 1.5 0 0 1 4.5 18V6A1.5 1.5 0 0 1 6 4.5Z" fill="none" stroke="currentColor" strokeWidth="1.6" />
    <path d="M8 8h8M8 12h8M8 16h5" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.6" />
  </MenuIcon>
);

const GoalIcon = () => (
  <MenuIcon>
    <path d="M7 17.5V8.8A2.8 2.8 0 0 1 9.8 6h4.4A2.8 2.8 0 0 1 17 8.8v8.7" fill="none" stroke="currentColor" strokeWidth="1.7" strokeLinecap="round" />
    <path d="M9 9.5h6M9 12.5h6M9 15.5h4" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.7" />
    <path d="M5.5 17.5h13" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.7" />
  </MenuIcon>
);

const ProfileIcon = () => (
  <MenuIcon>
    <circle cx="12" cy="9" r="3" fill="none" stroke="currentColor" strokeWidth="1.6" />
    <path d="M6.5 19a5.5 5.5 0 0 1 11 0" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.6" />
  </MenuIcon>
);

const LoginIcon = () => (
  <MenuIcon>
    <path d="M10 7.5V6.5A1.5 1.5 0 0 1 11.5 5h6A1.5 1.5 0 0 1 19 6.5v11A1.5 1.5 0 0 1 17.5 19h-6A1.5 1.5 0 0 1 10 17.5v-1" fill="none" stroke="currentColor" strokeWidth="1.6" />
    <path d="M4.5 12h9" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.8" />
    <path d="M11.5 9.5l2.5 2.5-2.5 2.5" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" />
  </MenuIcon>
);

const RegisterIcon = () => (
  <MenuIcon>
    <circle cx="9" cy="9" r="2.5" fill="none" stroke="currentColor" strokeWidth="1.6" />
    <path d="M5.5 18a4 4 0 0 1 7 0" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.6" />
    <path d="M15 8.5v5" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.8" />
    <path d="M12.5 11h5" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.8" />
  </MenuIcon>
);

const LogoutIcon = () => (
  <MenuIcon>
    <path d="M13 5h4a1.5 1.5 0 0 1 1.5 1.5v11A1.5 1.5 0 0 1 17 19h-4" fill="none" stroke="currentColor" strokeWidth="1.6" />
    <path d="M12 12H4.5" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="1.8" />
    <path d="M8 8.5L4.5 12 8 15.5" fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.8" />
  </MenuIcon>
);

const AddIcon = () => (
  <MenuIcon>
    <path d="M12 5.5v13" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="2" />
    <path d="M5.5 12h13" fill="none" stroke="currentColor" strokeLinecap="round" strokeWidth="2" />
  </MenuIcon>
);

function Layout() {
  const location = useLocation();
  const navigate = useNavigate();
  const { isAuthenticated, logout } = useAuthStore();
  const { t } = useI18n();
  const isAuthPage = ['/login', '/register'].includes(location.pathname);

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  const navItemClassName = ({ isActive }) => `nav-menu-item nav-menu-link${isActive ? ' active' : ''}`;

  return (
    <div className="app-shell">
      {!isAuthPage ? (
        <header className="topbar">
          <Link to="/" className="brand">
            <img className="brand-logo" src="/logo.svg" alt="GloriousMagra" />
          </Link>
        </header>
      ) : null}
      <main className="container">
        <Outlet />
      </main>
      <footer className="app-dock-footer">
        <nav className="nav-links" aria-label={t('nav.menu')}>
          {isAuthenticated ? (
            <>
              <NavLink to="/" className={navItemClassName} aria-label={t('nav.progress')}>
                <ProgressIcon />
                <span>{t('nav.progress')}</span>
              </NavLink>
              <NavLink to="/my-measurements" className={navItemClassName} aria-label={t('nav.myMeasurements')}>
                <MeasurementsIcon />
                <span>{t('nav.myMeasurements')}</span>
              </NavLink>
              <NavLink to="/goals" className={navItemClassName} aria-label={t('nav.goals')}>
                <GoalIcon />
                <span>{t('nav.goals')}</span>
              </NavLink>
              <NavLink
                to="/measurements/new"
                className={({ isActive }) => `${navItemClassName({ isActive })} nav-menu-add-button`}
                aria-label={t('dashboard.addMeasurement')}
              >
                <AddIcon />
                <span>{t('dashboard.addMeasurement')}</span>
              </NavLink>
              <NavLink to="/profile" className={({ isActive }) => `${navItemClassName({ isActive })} nav-profile-link`} aria-label={t('nav.profile')}>
                <ProfileIcon />
                <span>{t('nav.profile')}</span>
              </NavLink>
              <button className="secondary nav-menu-item nav-menu-button" onClick={handleLogout} aria-label={t('nav.logout')}>
                <LogoutIcon />
                <span>{t('nav.logout')}</span>
              </button>
            </>
          ) : (
            <>
              <NavLink to="/login" className={navItemClassName} aria-label={t('nav.login')}>
                <LoginIcon />
                <span>{t('nav.login')}</span>
              </NavLink>
              <NavLink to="/register" className={navItemClassName} aria-label={t('nav.register')}>
                <RegisterIcon />
                <span>{t('nav.register')}</span>
              </NavLink>
            </>
          )}
        </nav>
      </footer>
    </div>
  );
}

export default Layout;
