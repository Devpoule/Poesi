export type ApiResponse<T> = {
  status: boolean;
  type: string;
  code: string;
  message: string | null;
  data: T;
  errors: Record<string, string[]> | null;
  meta: Record<string, unknown> | null;
};
