import { Navigate } from 'react-router-dom'
import { useAuthStore } from '@/store/authStore'

export default function RespondentGuard({ children }) {
  const { getRespondentSession } = useAuthStore()
  const session = getRespondentSession()
  if (!session) return <Navigate to="/respondent/login" replace />
  return children
}
