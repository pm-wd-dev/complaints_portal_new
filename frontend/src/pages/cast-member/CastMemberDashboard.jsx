import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import PortalLayout from '@/components/layout/PortalLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import { supabase } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import { formatDateTime, getStatusLabel } from '@/lib/utils'
import { FileText, Plus, Eye, TrendingUp, CheckCircle, Clock } from 'lucide-react'
import toast from 'react-hot-toast'

const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

export default function CastMemberDashboard() {
  const { user, profile, signOut } = useAuthStore()
  const navigate = useNavigate()
  const [complaints, setComplaints] = useState([])
  const [loading, setLoading] = useState(true)
  const [stats, setStats] = useState({ total: 0, resolved: 0, under_review: 0 })

  useEffect(() => {
    if (!user) { navigate('/cast-member/login'); return }
    load()
  }, [user])

  const load = async () => {
    setLoading(true)
    const { data } = await supabase
      .from('complaints')
      .select('*, stages(name,color)')
      .order('created_at', { ascending: false })
    const list = data || []
    setComplaints(list)
    setStats({
      total: list.length,
      resolved: list.filter(c => c.status === 'resolved').length,
      under_review: list.filter(c => c.status === 'under_review').length,
    })
    setLoading(false)
  }

  const handleSignOut = async () => {
    await signOut()
    navigate('/cast-member/login')
  }

  const statCards = [
    { label: 'Total', value: stats.total, icon: FileText, color: 'text-blue-700', bg: 'bg-blue-50' },
    { label: 'Under Review', value: stats.under_review, icon: TrendingUp, color: 'text-yellow-700', bg: 'bg-yellow-50' },
    { label: 'Resolved', value: stats.resolved, icon: CheckCircle, color: 'text-green-700', bg: 'bg-green-50' },
  ]

  return (
    <PortalLayout title="Cast Member Portal" role="cast_member" onSignOut={handleSignOut} userName={profile?.name}>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h2 className="text-xl font-bold text-gray-900">Dashboard</h2>
          <p className="text-gray-500 text-sm mt-0.5">Overview of all complaints</p>
        </div>
        <Link to="/cast-member/complaints/create">
          <Button><Plus size={15} /> New Complaint</Button>
        </Link>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-3 gap-4 mb-6">
        {statCards.map(({ label, value, icon: Icon, color, bg }) => (
          <Card key={label}>
            <CardContent className="py-4">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-xs text-gray-500">{label}</p>
                  <p className="text-2xl font-bold text-gray-900">{loading ? '...' : value}</p>
                </div>
                <div className={`w-9 h-9 ${bg} rounded-xl flex items-center justify-center`}>
                  <Icon size={16} className={color} />
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Complaints Table */}
      <Card>
        <CardHeader>
          <div className="flex items-center justify-between">
            <CardTitle>All Complaints</CardTitle>
          </div>
        </CardHeader>
        <CardContent className="p-0">
          {loading ? (
            <div className="p-8 text-center text-gray-400">Loading...</div>
          ) : complaints.length === 0 ? (
            <div className="p-8 text-center text-gray-400">No complaints yet</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-gray-100 bg-gray-50 text-left">
                    <th className="px-4 py-3 font-medium text-gray-500">Case #</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Description</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Status</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Stage</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Date</th>
                    <th className="px-4 py-3 font-medium text-gray-500"></th>
                  </tr>
                </thead>
                <tbody>
                  {complaints.map(c => (
                    <tr key={c.id} className="border-b border-gray-50 hover:bg-gray-50">
                      <td className="px-4 py-3 font-mono text-xs text-blue-700">{c.case_number}</td>
                      <td className="px-4 py-3 text-gray-600 max-w-xs truncate">{c.description}</td>
                      <td className="px-4 py-3">
                        <Badge variant={statusVariant[c.status] || 'default'}>{getStatusLabel(c.status)}</Badge>
                      </td>
                      <td className="px-4 py-3">
                        {c.stages ? (
                          <span className="text-xs px-2 py-0.5 rounded-full" style={{ background: c.stages.color + '20', color: c.stages.color }}>
                            {c.stages.name}
                          </span>
                        ) : <span className="text-gray-400 text-xs">—</span>}
                      </td>
                      <td className="px-4 py-3 text-gray-400 text-xs">{formatDateTime(c.created_at)}</td>
                      <td className="px-4 py-3">
                        <Link to={`/cast-member/complaints/${c.id}`}>
                          <Button variant="ghost" size="sm"><Eye size={13} /> View</Button>
                        </Link>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </CardContent>
      </Card>
    </PortalLayout>
  )
}
