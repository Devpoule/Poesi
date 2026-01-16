import type { Poem } from '../model/Poem';
import type { PoemRepository } from '../repository/PoemRepository';

export type CreatePoemPayload = {
  title: string;
  content: string;
  moodColor: string;
};

export async function createPoem(repository: PoemRepository, payload: CreatePoemPayload): Promise<Poem> {
  return repository.create(payload);
}

