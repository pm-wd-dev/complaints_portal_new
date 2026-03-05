import { Navigate } from 'react-router-dom'
import { useAuthStore } from '@/store/authStore'

export default function RespondentGuard({ children }) {
  const { user, profile, loading } = useAuthStore()

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-2" />
          <p className="text-sm text-gray-500">Loading...</p>
        </div>
      </div>
    )
  }

  if (!user || !profile) return <Navigate to="/login" replace />
  if (profile.role !== 'respondent') return <Navigate to="/login" replace />

  return children
}
