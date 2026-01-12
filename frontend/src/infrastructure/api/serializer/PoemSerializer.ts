import type { Poem } from '../../../domain/poem/model/Poem';

export type PoemDto = {
  id: number;
  title: string;
  status: string | null;
  moodColor: string | null;
  symbolType: string | null;
  createdAt: string | null;
  publishedAt: string | null;
  user: {
    id: number | null;
    pseudo: string | null;
    totemId: number | null;
  } | null;
};

export function fromApi(dto: PoemDto): Poem {
  return {
    id: dto.id,
    title: dto.title,
    status: dto.status,
    moodColor: dto.moodColor,
    symbolType: dto.symbolType,
    createdAt: dto.createdAt,
    publishedAt: dto.publishedAt,
    user: dto.user
      ? {
          id: dto.user.id,
          pseudo: dto.user.pseudo,
          totemId: dto.user.totemId,
        }
      : null,
  };
}
