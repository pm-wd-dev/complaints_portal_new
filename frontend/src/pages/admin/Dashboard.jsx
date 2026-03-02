import { useEffect, useState } from 'react'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import { supabase, getErrorMessage } from '@/lib/supabase'
import { formatDateTime, getStatusColor, getStatusLabel } from '@/lib/utils'
import { FileText, Clock, CheckCircle, AlertTriangle, Users, TrendingUp } from 'lucide-react'
import { Link } from 'react-router-dom'
import toast from 'react-hot-toast'

export default function Dashboard() {
  const [stats, setStats] = useState({ total: 0, submitted: 0, under_review: 0, resolved: 0, escalated: 0 })
  const [recent, setRecent] = useState([])
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const load = async () => {
      try {
        const [{ data: complaints }, { count: total }] = await Promise.all([
          supabase.from('complaints').select('*').order('created_at', { ascending: false }).limit(8),
          supabase.from('complaints').select('*', { count: 'exact', head: true }),
        ])
        const byStatus = (complaints || []).reduce((acc, c) => {
          acc[c.status] = (acc[c.status] || 0) + 1
          return acc
        }, {})
        setStats({ total: total || 0, ...byStatus })
        setRecent(complaints || [])
      } catch (err) {
        toast.error(getErrorMessage(err))
      } finally {
        setLoading(false)
      }
    }
    load()
  }, [])

  const statCards = [
    { label: 'Total Complaints', value: stats.total,       icon: FileText,       color: 'text-blue-700',   bg: 'bg-blue-50' },
    { label: 'Submitted',        value: stats.submitted||0, icon: Clock,          color: 'text-yellow-700', bg: 'bg-yellow-50' },
    { label: 'Under Review',     value: stats.under_review||0, icon: TrendingUp,  color: 'text-orange-700', bg: 'bg-orange-50' },
    { label: 'Resolved',         value: stats.resolved||0,  icon: CheckCircle,    color: 'text-green-700',  bg: 'bg-green-50' },
  ]

  const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

  return (
    <AdminLayout>
      <div className="mb-6">
        <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-gray-500 text-sm mt-1">Overview of complaint management system</p>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {statCards.map(({ label, value, icon: Icon, color, bg }) => (
          <Card key={label}>
            <CardContent className="py-5">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-xs text-gray-500 mb-1">{label}</p>
                  <p className="text-2xl font-bold text-gray-900">{loading ? '...' : value}</p>
                </div>
                <div className={`w-10 h-10 ${bg} rounded-xl flex items-center justify-center`}>
                  <Icon size={18} className={color} />
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Recent Complaints */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>Recent Complaints</CardTitle>
            <Link to="/admin/complaints" className="text-sm text-blue-700 hover:underline">View all</Link>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          {loading ? (
            <div className="p-8 text-center text-gray-400 text-sm">Loading...</div>
          ) : recent.length === 0 ? (
            <div className="p-8 text-center text-gray-400 text-sm">No complaints yet</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-gray-100 bg-gray-50">
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Case #</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Description</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Status</th>
                    <th className="px-4 py-3 text-left font-medium text-gray-500">Date</th>
                  </tr>
                </thead>
                <tbody>
                  {recent.map(c => (
                    <tr key={c.id} className="border-b border-gray-50 hover:bg-gray-50">
                      <td className="px-4 py-3">
                        <Link to={`/admin/complaints/${c.id}`} className="font-mono text-xs text-blue-700 hover:underline">
                          {c.case_number}
                        </Link>
                      </td>
                      <td className="px-4 py-3 text-gray-700 max-w-xs truncate">{c.description}</td>
                      <td className="px-4 py-3">
                        <Badge variant={statusVariant[c.status] || 'default'}>{getStatusLabel(c.status)}</Badge>
                      </td>
                      <td className="px-4 py-3 text-gray-500 text-xs">{formatDateTime(c.created_at)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </CardContent>
      </Card>
    </AdminLayout>
  )
}
