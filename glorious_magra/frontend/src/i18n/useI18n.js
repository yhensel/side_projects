import { useMemo } from 'react';
import { createTranslator } from './language';
import { usePreferencesStore } from '../stores/preferencesStore';

export function useI18n() {
  const language = usePreferencesStore((state) => state.language);

  const t = useMemo(() => createTranslator(language), [language]);

  return { language, t };
}
