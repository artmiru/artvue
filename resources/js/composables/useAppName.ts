export function useAppName(): string {
  return import.meta.env.VITE_APP_NAME || 'АртМир';
}
