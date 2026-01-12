import { Redirect } from 'expo-router';
import { useAuth } from '../src/bootstrap/AuthProvider'; 

export default function Index() {
  const { tokens, isLoading } = useAuth();

  if (isLoading) {
    return null;
  }

  return <Redirect href="/(tabs)/home" />;
}
