import { SUPPORTED_LANGUAGES, translations } from './translations';

export const DEFAULT_LANGUAGE = 'en';

export function normalizeLanguage(candidate) {
  if (!candidate || typeof candidate !== 'string') {
    return DEFAULT_LANGUAGE;
  }

  const normalized = candidate.toLowerCase().split('-')[0];
  return SUPPORTED_LANGUAGES.includes(normalized) ? normalized : DEFAULT_LANGUAGE;
}

export function getDeviceLanguage() {
  if (typeof navigator === 'undefined') {
    return DEFAULT_LANGUAGE;
  }

  return normalizeLanguage(navigator.language || DEFAULT_LANGUAGE);
}

export function createTranslator(language) {
  const lang = translations[language] ? language : DEFAULT_LANGUAGE;

  return (key, vars = {}) => {
    const value = key.split('.').reduce((acc, part) => acc?.[part], translations[lang]);

    if (typeof value !== 'string') {
      return key;
    }

    return value.replace(/\{(\w+)\}/g, (_, token) => String(vars[token] ?? `{${token}}`));
  };
}
