import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Select from '@/components/ui/Select'
import { supabase } from '@/lib/supabase'
import { formatDateTime, getStatusLabel } from '@/lib/utils'
import { Plus, Search, Eye } from 'lucide-react'

const STATUS_OPTIONS = ['all','submitted','under_review','escalated','resolved','closed']
const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

export default function ComplaintsListPage() {
  const [complaints, setComplaints] = useState([])
  const [loading, setLoading] = useState(true)
  const [search, setSearch] = useState('')
  const [status, setStatus] = useState('all')
  const navigate = useNavigate()

  useEffect(() => { load() }, [status])

  const load = async () => {
    setLoading(true)
    let q = supabase.from('complaints').select('*, stages(name,color)').order('created_at', { ascending: false })
    if (status !== 'all') q = q.eq('status', status)
    const { data } = await q
    setComplaints(data || [])
    setLoading(false)
  }

  const filtered = complaints.filter(c =>
    !search || c.case_number.toLowerCase().includes(search.toLowerCase()) ||
    c.description?.toLowerCase().includes(search.toLowerCase()) ||
    c.name?.toLowerCase().includes(search.toLowerCase())
  )

  return (
    <AdminLayout>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Complaints</h1>
          <p className="text-gray-500 text-sm mt-1">{complaints.length} total complaints</p>
        </div>
        <Button onClick={() => navigate('/admin/complaints/create')} className="flex items-center gap-2">
          <Plus size={16} /> New Complaint
        </Button>
      </div>

      {/* Filters */}
      <div className="flex flex-col sm:flex-row gap-3 mb-5">
        <div className="flex-1 relative">
          <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            value={search}
            onChange={e => setSearch(e.target.value)}
            placeholder="Search by case number, name, or description..."
            className="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500"
          />
        </div>
        <select
          value={status}
          onChange={e => setStatus(e.target.value)}
          className="px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-blue-500"
        >
          {STATUS_OPTIONS.map(s => (
            <option key={s} value={s}>{s === 'all' ? 'All Status' : getStatusLabel(s)}</option>
          ))}
        </select>
      </div>

      <Card>
        <CardContent className="p-0">
          {loading ? (
            <div className="p-10 text-center text-gray-400">Loading complaints...</div>
          ) : filtered.length === 0 ? (
            <div className="p-10 text-center text-gray-400">No complaints found</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-gray-100 bg-gray-50 text-left">
                    <th className="px-4 py-3 font-medium text-gray-500">Case #</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Name</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Description</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Status</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Stage</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Date</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {filtered.map(c => (
                    <tr key={c.id} className="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                      <td className="px-4 py-3 font-mono text-xs text-blue-700">{c.case_number}</td>
                      <td className="px-4 py-3 text-gray-700">{c.name || (c.anonymity ? 'Anonymous' : 'N/A')}</td>
                      <td className="px-4 py-3 text-gray-600 max-w-xs truncate">{c.description}</td>
                      <td className="px-4 py-3">
                        <Badge variant={statusVariant[c.status] || 'default'}>{getStatusLabel(c.status)}</Badge>
                      </td>
                      <td className="px-4 py-3">
                        {c.stages ? (
                          <span className="text-xs px-2 py-1 rounded-full" style={{ background: c.stages.color + '20', color: c.stages.color }}>
                            {c.stages.name}
                          </span>
                        ) : <span className="text-gray-400 text-xs">—</span>}
                      </td>
                      <td className="px-4 py-3 text-gray-400 text-xs">{formatDateTime(c.created_at)}</td>
                      <td className="px-4 py-3">
                        <Link to={`/admin/complaints/${c.id}`}>
                          <Button variant="ghost" size="sm" className="flex items-center gap-1">
                            <Eye size={14} /> View
                          </Button>
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
    </AdminLayout>
  )
}
