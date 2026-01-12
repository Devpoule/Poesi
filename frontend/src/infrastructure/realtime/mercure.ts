import { config } from '../../support/config/env';

export type MercureMessage<T> = {
  topic: string;
  data: T;
};

export type MercureOptions = {
  topics: string[];
  onMessage: (message: MercureMessage<unknown>) => void;
};

export function connectMercure(options: MercureOptions): EventSource | null {
  if (typeof EventSource === 'undefined') {
    // React Native needs an EventSource polyfill (ex: react-native-sse).
    return null;
  }

  const url = new URL(config.mercureHubUrl);
  for (const topic of options.topics) {
    url.searchParams.append('topic', topic);
  }

  const source = new EventSource(url.toString());
  source.onmessage = (event) => {
    options.onMessage({
      topic: 'unknown',
      data: JSON.parse(event.data),
    });
  };

  return source;
}
