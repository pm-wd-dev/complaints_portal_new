import { Navigate } from 'react-router-dom'
import { useAuthStore } from '@/store/authStore'

export default function LawyerGuard({ children }) {
  const { getLawyerSession } = useAuthStore()
  const session = getLawyerSession()
  if (!session) return <Navigate to="/lawyer/login" replace />
  return children
}
