import { Navigate, Route, Routes } from 'react-router-dom';
import { useEffect, useState } from 'react';
import Layout from './components/Layout';
import LoginPage from './pages/LoginPage.jsx';
import RegisterPage from './pages/RegisterPage.jsx';
import DashboardPage from './pages/DashboardPage.jsx';
import ProfilePage from './pages/ProfilePage.jsx';
import MyMeasurementsPage from './pages/MyMeasurementsPage.jsx';
import AddMeasurementPage from './pages/AddMeasurementPage.jsx';
import GoalsPage from './pages/GoalsPage.jsx';
import { useAuthStore } from './stores/authStore';

const ProtectedRoute = ({ children }) => {
  const isAuthenticated = useAuthStore((state) => state.isAuthenticated);
  return isAuthenticated ? children : <Navigate to="/login" replace />;
};

function App() {
  const initialize = useAuthStore((state) => state.initialize);
  const [isSplashVisible, setIsSplashVisible] = useState(true);

  useEffect(() => {
    initialize();

    const timer = window.setTimeout(() => {
      setIsSplashVisible(false);
    }, 900);

    return () => window.clearTimeout(timer);
  }, [initialize]);

  return (
    <>
      <div className={`app-splash${isSplashVisible ? '' : ' hidden'}`} aria-live="polite" aria-label="Loading GloriousMagra">
        <div className="app-splash-inner">
          <div className="app-splash-logo-wrap">
            <img className="app-splash-logo" src="/logo.svg" alt="GloriousMagra" />
          </div>
          <div className="app-splash-text">
            <span className="app-splash-brand">GLORIUS</span>
            <span className="app-splash-brand-light">MAGRA</span>
          </div>
        </div>
      </div>

      <Routes>
        <Route element={<Layout />}>
          <Route path="/login" element={<LoginPage />} />
          <Route path="/register" element={<RegisterPage />} />
          <Route
            path="/"
            element={
              <ProtectedRoute>
                <DashboardPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/profile"
            element={
              <ProtectedRoute>
                <ProfilePage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/my-measurements"
            element={
              <ProtectedRoute>
                <MyMeasurementsPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/measurements/new"
            element={
              <ProtectedRoute>
                <AddMeasurementPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/goals"
            element={
              <ProtectedRoute>
                <GoalsPage />
              </ProtectedRoute>
            }
          />
        </Route>
      </Routes>
    </>
  );
}

export default App;
