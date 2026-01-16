import { useState } from 'react';
import { useAuth } from '../../../bootstrap/AuthProvider';
import { ApiError } from '../../../infrastructure/api/client';

export function useLoginViewModel() {
  const { login } = useAuth();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string[]>>({});

  async function submit() {
    setError(null);
    setFieldErrors({});
    setIsSubmitting(true);
    try {
      await login(email, password);
    } catch (err) {
      if (err instanceof ApiError) {
        setError(err.message);
        setFieldErrors(err.errors ?? {});
      } else if (err instanceof Error) {
        setError(err.message);
      } else {
        setError('Connexion impossible.');
      }
    } finally {
      setIsSubmitting(false);
    }
  }

  return {
    email,
    password,
    isSubmitting,
    error,
    fieldErrors,
    setEmail,
    setPassword,
    submit,
  };
}
