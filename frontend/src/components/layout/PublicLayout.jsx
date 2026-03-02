import { Link } from 'react-router-dom'
import { FileText, Search } from 'lucide-react'

export default function PublicLayout({ children }) {
  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      <header className="bg-white border-b border-gray-200 flex-shrink-0" style={{ boxShadow: '0 1px 3px rgba(0,0,0,0.06)' }}>
        <div className="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
          <Link to="/" className="flex items-center gap-2.5">
            <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center font-bold text-white text-xs tracking-tight">
              CM
            </div>
            <span className="font-semibold text-gray-900">Complaint Portal</span>
          </Link>

          <nav className="flex items-center gap-1 text-sm">
            <Link
              to="/submit"
              className="flex items-center gap-1.5 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-blue-600 transition-colors font-medium"
            >
              <FileText size={14} /> Submit
            </Link>
            <Link
              to="/track"
              className="flex items-center gap-1.5 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-100 hover:text-blue-600 transition-colors font-medium"
            >
              <Search size={14} /> Track
            </Link>
            <Link
              to="/login"
              className="ml-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors"
            >
              Staff Login
            </Link>
          </nav>
        </div>
      </header>

      <main className="flex-1">{children}</main>

      <footer className="bg-white border-t border-gray-200 text-gray-400 text-center py-4 text-xs">
        &copy; {new Date().getFullYear()} Complaint Management System. All rights reserved.
      </footer>
    </div>
  )
}
