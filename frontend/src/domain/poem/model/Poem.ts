export type PoemUser = {
  id: number | null;
  pseudo: string | null;
  totemId: number | null;
};

export type Poem = {
  id: number;
  title: string;
  status: string | null;
  moodColor: string | null;
  symbolType: string | null;
  createdAt: string | null;
  publishedAt: string | null;
  user: PoemUser | null;
};
