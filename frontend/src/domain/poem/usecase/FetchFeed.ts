import type { PoemListParams, PoemPage, PoemRepository } from '../repository/PoemRepository';

export async function fetchFeed(
  repository: PoemRepository,
  params?: PoemListParams
): Promise<PoemPage> {
  return repository.list(params);
}
