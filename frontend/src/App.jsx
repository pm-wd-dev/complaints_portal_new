import { useEffect } from 'react'
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { Toaster } from 'react-hot-toast'
import { useAuthStore } from '@/store/authStore'
import AdminGuard from '@/components/guards/AdminGuard'
import RespondentGuard from '@/components/guards/RespondentGuard'
import LawyerGuard from '@/components/guards/LawyerGuard'

// Public pages
import HomePage from '@/pages/public/HomePage'
import SubmitComplaintPage from '@/pages/public/SubmitComplaintPage'
import TrackComplaintPage from '@/pages/public/TrackComplaintPage'

// Auth
import LoginPage from '@/pages/auth/LoginPage'

// Admin pages
import Dashboard from '@/pages/admin/Dashboard'
import ComplaintsListPage from '@/pages/admin/ComplaintsListPage'
import ComplaintDetailPage from '@/pages/admin/ComplaintDetailPage'
import CreateComplaintPage from '@/pages/admin/CreateComplaintPage'
import UsersPage from '@/pages/admin/UsersPage'
import StagesPage from '@/pages/admin/StagesPage'
import LocationsPage from '@/pages/admin/LocationsPage'

// Respondent
import RespondentDashboard from '@/pages/respondent/RespondentDashboard'
import RespondentComplaintDetail from '@/pages/respondent/RespondentComplaintDetail'

// Lawyer
import LawyerDashboard from '@/pages/lawyer/LawyerDashboard'
import LawyerComplaintDetail from '@/pages/lawyer/LawyerComplaintDetail'

// Cast Member
import CastMemberLoginPage from '@/pages/cast-member/CastMemberLoginPage'
import CastMemberDashboard from '@/pages/cast-member/CastMemberDashboard'
import CastMemberCreateComplaint from '@/pages/cast-member/CastMemberCreateComplaint'
import CastMemberComplaintDetail from '@/pages/cast-member/CastMemberComplaintDetail'

// Admin extras
import ResolutionPage from '@/pages/admin/ResolutionPage'

export default function App() {
  const { initialize } = useAuthStore()

  useEffect(() => {
    initialize()
  }, [])

  return (
    <BrowserRouter>
      <Toaster
        position="top-right"
        toastOptions={{
          duration: 4000,
          style: { fontSize: '14px', maxWidth: '380px' },
        }}
      />
      <Routes>
        {/* Public */}
        <Route path="/" element={<HomePage />} />
        <Route path="/submit" element={<SubmitComplaintPage />} />
        <Route path="/track" element={<TrackComplaintPage />} />

        {/* Auth */}
        <Route path="/login" element={<LoginPage />} />

        {/* Admin (protected) */}
        <Route path="/admin" element={<AdminGuard><Dashboard /></AdminGuard>} />
        <Route path="/admin/complaints" element={<AdminGuard><ComplaintsListPage /></AdminGuard>} />
        <Route path="/admin/complaints/create" element={<AdminGuard><CreateComplaintPage /></AdminGuard>} />
        <Route path="/admin/complaints/:id" element={<AdminGuard><ComplaintDetailPage /></AdminGuard>} />
        <Route path="/admin/users" element={<AdminGuard><UsersPage /></AdminGuard>} />
        <Route path="/admin/stages" element={<AdminGuard><StagesPage /></AdminGuard>} />
        <Route path="/admin/locations" element={<AdminGuard><LocationsPage /></AdminGuard>} />
        <Route path="/admin/complaints/:id/resolution" element={<AdminGuard><ResolutionPage /></AdminGuard>} />

        {/* Respondent */}
        <Route path="/respondent/login" element={<Navigate to="/login" replace />} />
        <Route path="/respondent/dashboard" element={<RespondentGuard><RespondentDashboard /></RespondentGuard>} />
        <Route path="/respondent/complaints/:id" element={<RespondentGuard><RespondentComplaintDetail /></RespondentGuard>} />

        {/* Lawyer */}
        <Route path="/lawyer/login" element={<Navigate to="/login" replace />} />
        <Route path="/lawyer/dashboard" element={<LawyerGuard><LawyerDashboard /></LawyerGuard>} />
        <Route path="/lawyer/complaints/:id" element={<LawyerGuard><LawyerComplaintDetail /></LawyerGuard>} />

        {/* Cast Member */}
        <Route path="/cast-member/login" element={<CastMemberLoginPage />} />
        <Route path="/cast-member/dashboard" element={<CastMemberDashboard />} />
        <Route path="/cast-member/complaints/create" element={<CastMemberCreateComplaint />} />
        <Route path="/cast-member/complaints/:id" element={<CastMemberComplaintDetail />} />

        {/* Fallback */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </BrowserRouter>
  )
}
