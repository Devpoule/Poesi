import { useAuth } from '../../../bootstrap/AuthProvider';
import { WriteAccessGate } from './components/WriteAccessGate';
import { WriteEditor } from './components/WriteEditor';

/**
 * Entry point for the Write tab with auth gating.
 */
export default function WriteScreen() {
  const { tokens } = useAuth();

  if (!tokens) {
    return <WriteAccessGate />;
  }

  return <WriteEditor />;
}
