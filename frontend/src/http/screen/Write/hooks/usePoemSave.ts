import { useState } from 'react';
import type { Poem } from '../../../../domain/poem/model/Poem';
import { createPoem, type CreatePoemPayload } from '../../../../domain/poem/usecase/CreatePoem';
import { poemRepository } from '../../../../bootstrap/repositories';
import { ApiError } from '../../../../infrastructure/api/client';
import { useRef } from 'react';

const moodToApi: Record<string, string> = {
  neutre: 'grey',
  rouge: 'red',
  orange: 'orange',
  jaune: 'yellow',
  vert: 'green',
  bleu: 'blue',
  indigo: 'indigo',
  violet: 'violet',
  blanc: 'white',
  noir: 'black',
  gris: 'grey',
};

const normalizeMoodForApi = (mood: string) => moodToApi[mood.toLowerCase()] ?? 'grey';

export function usePoemSave() {
  const lastSignature = useRef<string | null>(null);
  const [isSaving, setIsSaving] = useState(false);
  const [lastPoem, setLastPoem] = useState<Poem | null>(null);
  const [error, setError] = useState<string | null>(null);

  const buildSignature = (payload: CreatePoemPayload) =>
    `${payload.title.trim()}|${payload.content.trim()}|${payload.moodColor}`;

  const save = async (payload: CreatePoemPayload) => {
    setIsSaving(true);
    setError(null);
    try {
      const normalizedPayload: CreatePoemPayload = {
        ...payload,
        moodColor: normalizeMoodForApi(payload.moodColor),
      };
      const sig = buildSignature(normalizedPayload);
      if (lastSignature.current === sig) {
        setError('Ce brouillon est deja enregistre.');
        return lastPoem;
      }

      const poem = await createPoem(poemRepository, normalizedPayload);
      setLastPoem(poem);
      lastSignature.current = sig;
      return poem;
    } catch (e) {
      const message =
        e instanceof ApiError
          ? e.message
          : e instanceof Error
            ? e.message
            : 'Enregistrement impossible.';
      setError(message);
      throw e;
    } finally {
      setIsSaving(false);
    }
  };

  return { isSaving, lastPoem, error, save };
}
