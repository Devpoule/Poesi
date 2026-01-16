import type { PoemRepository, PoemListParams, PoemPage, PoemPagination } from '../../domain/poem/repository/PoemRepository';
import type { ApiResponse } from '../../support/http/apiResponse';
import { apiFetch } from '../api/client';
import { fromApi, type PoemDto } from '../api/serializer/PoemSerializer';

type PoemListMeta = {
  pagination?: PoemPagination;
};

export class ApiPoemRepository implements PoemRepository {
  async list(params?: PoemListParams): Promise<PoemPage> {
    const query = new URLSearchParams();
    if (params?.page) {
      query.set('page', String(params.page));
    }
    if (params?.limit) {
      query.set('limit', String(params.limit));
    }
    if (params?.sort) {
      query.set('sort', params.sort);
    }
    if (params?.direction) {
      query.set('direction', params.direction);
    }

    const suffix = query.toString() ? `?${query.toString()}` : '';
    const response = await apiFetch<ApiResponse<PoemDto[]>>(`/api/poems${suffix}`);

    if (!response.status || !response.data) {
      throw new Error(response.message ?? 'Impossible de charger le feed.');
    }

    const pagination = (response.meta as PoemListMeta | null)?.pagination ?? null;

    return {
      items: response.data.map(fromApi),
      pagination,
    };
  }

  async create(payload: { title: string; content: string; moodColor: string }) {
    const response = await apiFetch<ApiResponse<PoemDto>>('/api/poems', {
      method: 'POST',
      body: JSON.stringify({
        title: payload.title,
        content: payload.content,
        moodColor: payload.moodColor,
      }),
    });

    if (!response.status || !response.data) {
      throw new Error(response.message ?? 'Impossible de sauvegarder le texte.');
    }

    return fromApi(response.data);
  }
}

