import type { Poem } from '../model/Poem';

export type PoemListParams = {
  page?: number;
  limit?: number;
  sort?: string;
  direction?: 'ASC' | 'DESC';
};

export type PoemPagination = {
  page: number;
  limit: number;
  total: number;
  pages: number;
  sort: string;
  direction: string;
};

export type PoemPage = {
  items: Poem[];
  pagination: PoemPagination | null;
};

export type PoemRepository = {
  list: (params?: PoemListParams) => Promise<PoemPage>;
  create: (payload: { title: string; content: string; moodColor: string }) => Promise<Poem>;
};
