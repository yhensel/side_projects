import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export const useAuthStore = create(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      refreshToken: null,
      isAuthenticated: false,
      initialize: () => {
        const token = localStorage.getItem('gloriousmagra.token');
        const user = JSON.parse(localStorage.getItem('gloriousmagra.user') || 'null');

        const refreshToken = localStorage.getItem('gloriousmagra.refreshToken');

        if (token && user && refreshToken) {
          set({ token, refreshToken, user, isAuthenticated: true });
        }
      },
      login: (user, token, refreshToken) => set({ user, token, refreshToken, isAuthenticated: true }),
      logout: () => {
        localStorage.removeItem('gloriousmagra.token');
        localStorage.removeItem('gloriousmagra.user');
        localStorage.removeItem('gloriousmagra.refreshToken');
        set({ user: null, token: null, refreshToken: null, isAuthenticated: false });
      },
    }),
    {
      name: 'gloriousmagra-auth',
      partialize: (state) => ({ user: state.user, token: state.token, refreshToken: state.refreshToken, isAuthenticated: state.isAuthenticated }),
      onRehydrateStorage: () => (state) => {
        if (state?.token && state?.user) {
          localStorage.setItem('gloriousmagra.token', state.token);
          localStorage.setItem('gloriousmagra.user', JSON.stringify(state.user));
          localStorage.setItem('gloriousmagra.refreshToken', state.refreshToken);
        }
      },
    },
  ),
);
