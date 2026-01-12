export type ApiConfig = {
  baseUrl: string;
  mercureHubUrl: string;
};

export const config: ApiConfig = {
  baseUrl: 'https://127.0.0.1:8000',
  mercureHubUrl: 'http://localhost:3000/.well-known/mercure',
};
