export type ApiConfig = {
  baseUrl: string;
  mercureHubUrl: string;
};

export const config: ApiConfig = {
  // Use http for local dev to avoid TLS issues with self-signed certs.
  // On device/emulator, replace "localhost" with your machine IP (ex: http://192.168.0.12:8000).
  baseUrl: 'http://localhost:8000',
  mercureHubUrl: 'http://localhost:3000/.well-known/mercure',
};
