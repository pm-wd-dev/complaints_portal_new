import { useState } from 'react'
import { Link, useLocation, useNavigate } from 'react-router-dom'
import { useAuthStore } from '@/store/authStore'
import {
  LayoutDashboard, FileText, Users, MapPin, Layers,
  LogOut, Menu, X,
} from 'lucide-react'
import { cn } from '@/lib/utils'

const navItems = [
  { label: 'Dashboard',  icon: LayoutDashboard, to: '/admin' },
  { label: 'Complaints', icon: FileText,         to: '/admin/complaints' },
  { label: 'Users',      icon: Users,            to: '/admin/users' },
  { label: 'Locations',  icon: MapPin,           to: '/admin/locations' },
  { label: 'Stages',     icon: Layers,           to: '/admin/stages' },
]

export default function AdminLayout({ children }) {
  const [sidebarOpen, setSidebarOpen] = useState(true)
  const { profile, signOut } = useAuthStore()
  const location = useLocation()
  const navigate = useNavigate()

  const handleSignOut = async () => {
    await signOut()
    navigate('/login')
  }

  return (
    <div className="flex h-screen bg-gray-100 overflow-hidden">
      {/* Sidebar — white, like original Laravel */}
      <aside
        className={cn(
          'flex flex-col bg-white border-r border-gray-200 transition-all duration-300 flex-shrink-0',
          sidebarOpen ? 'w-[280px]' : 'w-16'
        )}
        style={{ boxShadow: '2px 0 4px rgba(0,0,0,0.07)' }}
      >
        {/* Logo */}
        <div className="flex items-center gap-3 px-5 py-5 border-b border-gray-100">
          <div className="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center font-bold text-white text-sm flex-shrink-0">
            CM
          </div>
          {sidebarOpen && (
            <div>
              <p className="font-semibold text-gray-900 text-sm leading-tight">Complaint System</p>
              <p className="text-xs text-gray-400 capitalize">{profile?.role?.replace('_',' ') || 'Admin'}</p>
            </div>
          )}
        </div>

        {/* Nav */}
        <nav className="flex-1 py-3 overflow-y-auto">
          {navItems.map(({ label, icon: Icon, to }) => {
            const active = location.pathname === to || (to !== '/admin' && location.pathname.startsWith(to))
            return (
              <Link
                key={to}
                to={to}
                title={!sidebarOpen ? label : undefined}
                className={cn(
                  'flex items-center gap-3 mx-2 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors',
                  active
                    ? 'bg-blue-50 text-blue-600'
                    : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800'
                )}
              >
                <Icon size={18} className="flex-shrink-0" />
                {sidebarOpen && <span>{label}</span>}
              </Link>
            )
          })}
        </nav>

        {/* Sign out */}
        <button
          onClick={handleSignOut}
          title={!sidebarOpen ? 'Sign out' : undefined}
          className="flex items-center gap-3 mx-2 mb-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-red-600 transition-colors border-t border-gray-100 mt-1 pt-3"
        >
          <LogOut size={18} className="flex-shrink-0" />
          {sidebarOpen && <span>Sign out</span>}
        </button>
      </aside>

      {/* Main area */}
      <div className="flex-1 flex flex-col overflow-hidden">
        {/* Top navbar — white, like original */}
        <header
          className="bg-white border-b border-gray-200 px-6 flex items-center gap-4 flex-shrink-0"
          style={{ height: '64px', boxShadow: '0 2px 4px rgba(0,0,0,0.06)' }}
        >
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors"
          >
            {sidebarOpen ? <X size={18} /> : <Menu size={18} />}
          </button>
          <div className="flex-1" />
          <div className="flex items-center gap-3">
            <span className="text-sm text-gray-500">Welcome, {profile?.name || 'Admin'}</span>
            <div className="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-semibold">
              {(profile?.name || 'A')[0].toUpperCase()}
            </div>
          </div>
        </header>

        {/* Page content */}
        <main className="flex-1 overflow-y-auto p-6">
          {children}
        </main>
      </div>
    </div>
  )
}
