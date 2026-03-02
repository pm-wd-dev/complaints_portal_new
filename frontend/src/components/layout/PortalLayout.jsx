import { LogOut } from 'lucide-react'

// Each portal gets its own navbar color matching the original Laravel app
const roleStyles = {
  respondent:  { nav: 'bg-white border-b border-gray-200', text: 'text-gray-700', btn: 'text-red-500 hover:text-red-700', accent: 'text-blue-600' },
  lawyer:      { nav: 'bg-white border-b border-gray-200', text: 'text-gray-700', btn: 'text-red-500 hover:text-red-700', accent: 'text-blue-600' },
  cast_member: { nav: 'bg-blue-600',                       text: 'text-white',    btn: 'text-white/80 hover:text-white',  accent: 'text-white' },
}

export default function PortalLayout({ children, title, role, onSignOut, userName }) {
  const s = roleStyles[role] || roleStyles.respondent

  return (
    <div className="min-h-screen flex flex-col bg-gray-100">
      <header className={`${s.nav} flex-shrink-0`} style={{ boxShadow: '0 2px 4px rgba(0,0,0,0.08)' }}>
        <div className="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className={`w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs ${role === 'cast_member' ? 'bg-white text-blue-600' : 'bg-blue-600 text-white'}`}>
              CM
            </div>
            <div>
              <p className={`font-semibold text-sm leading-tight ${s.text}`}>{title}</p>
              <p className={`text-xs capitalize ${role === 'cast_member' ? 'text-white/70' : 'text-gray-400'}`}>{role?.replace('_', ' ')}</p>
            </div>
          </div>
          <div className="flex items-center gap-4">
            {userName && <span className={`text-sm ${role === 'cast_member' ? 'text-white/80' : 'text-gray-500'}`}>Welcome, {userName}</span>}
            <button onClick={onSignOut} className={`flex items-center gap-1.5 text-sm font-medium transition-colors ${s.btn}`}>
              <LogOut size={15} /> Sign out
            </button>
          </div>
        </div>
      </header>
      <main className="flex-1 max-w-7xl mx-auto w-full px-6 py-6">
        {children}
      </main>
    </div>
  )
}
