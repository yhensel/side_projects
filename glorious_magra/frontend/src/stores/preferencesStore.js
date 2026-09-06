import { create } from 'zustand';
import { persist } from 'zustand/middleware';
import { getDeviceLanguage, normalizeLanguage } from '../i18n/language';

export const usePreferencesStore = create(
  persist(
    (set) => ({
      language: getDeviceLanguage(),
      setLanguage: (language) => set({ language: normalizeLanguage(language) }),
    }),
    {
      name: 'gloriousmagra-preferences',
    },
  ),
);
