import { useState } from 'react'
import PublicLayout from '@/components/layout/PublicLayout'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import { supabase } from '@/lib/supabase'
import { getStatusLabel, formatDate, formatDateTime } from '@/lib/utils'
import { Search, AlertCircle, FileText, CheckCircle, Clock, Layers } from 'lucide-react'
import toast from 'react-hot-toast'

const STATUS_STYLES = {
  submitted:    { dot: 'bg-blue-500',   label: 'Submitted',    text: 'text-blue-700',   bg: 'bg-blue-50  border-blue-200' },
  under_review: { dot: 'bg-yellow-500', label: 'Under Review', text: 'text-yellow-700', bg: 'bg-yellow-50 border-yellow-200' },
  escalated:    { dot: 'bg-orange-500', label: 'Escalated',    text: 'text-orange-700', bg: 'bg-orange-50 border-orange-200' },
  resolved:     { dot: 'bg-green-500',  label: 'Resolved',     text: 'text-green-700',  bg: 'bg-green-50  border-green-200' },
  closed:       { dot: 'bg-gray-400',   label: 'Closed',       text: 'text-gray-600',   bg: 'bg-gray-50   border-gray-200' },
}

function buildTimeline(complaint) {
  const events = []

  // 1. Submission event
  events.push({
    id: 'submitted',
    type: 'submitted',
    date: complaint.created_at,
    title: 'Complaint Submitted',
    body: complaint.description,
    icon: FileText,
    dotClass: 'bg-gray-900',
  })

  // 2. Stage changes (sorted oldest → newest)
  const stageLogs = [...(complaint.stage_change_logs || [])]
    .sort((a, b) => new Date(a.created_at) - new Date(b.created_at))

  stageLogs.forEach(log => {
    events.push({
      id: log.id,
      type: 'stage',
      date: log.created_at,
      title: `Moved to stage: ${log.to_stage?.name || 'Unknown'}`,
      body: log.note || null,
      icon: Layers,
      dotClass: 'bg-blue-500',
    })
  })

  // 3. If resolved or closed, add a final event
  if (['resolved', 'closed'].includes(complaint.status)) {
    const s = STATUS_STYLES[complaint.status]
    events.push({
      id: 'final',
      type: complaint.status,
      date: complaint.updated_at || complaint.created_at,
      title: `Complaint ${s.label}`,
      body: complaint.status === 'resolved'
        ? 'Your complaint has been reviewed and resolved by our team.'
        : 'This complaint has been closed.',
      icon: CheckCircle,
      dotClass: complaint.status === 'resolved' ? 'bg-green-500' : 'bg-gray-400',
    })
  }

  return events
}

export default function TrackComplaintPage() {
  const [caseNumber, setCaseNumber] = useState('')
  const [complaint, setComplaint]   = useState(null)
  const [loading, setLoading]       = useState(false)
  const [notFound, setNotFound]     = useState(false)

  const handleTrack = async (e) => {
    e.preventDefault()
    if (!caseNumber.trim()) return toast.error('Enter a case number')
    setLoading(true)
    setNotFound(false)
    setComplaint(null)
    try {
      const { data, error } = await supabase
        .from('complaints')
        .select('*, stages(*), stage_change_logs(*, from_stage:stages!stage_change_logs_from_stage_id_fkey(*), to_stage:stages!stage_change_logs_to_stage_id_fkey(*))')
        .eq('case_number', caseNumber.trim().toUpperCase())
        .single()
      if (error || !data) { setNotFound(true); return }
      setComplaint(data)
    } catch {
      setNotFound(true)
    } finally {
      setLoading(false)
    }
  }

  const status = complaint ? (STATUS_STYLES[complaint.status] || STATUS_STYLES.submitted) : null
  const timeline = complaint ? buildTimeline(complaint) : []

  return (
    <PublicLayout>
      <div className="max-w-2xl mx-auto px-6 py-12">

        {/* Header */}
        <div className="mb-8">
          <h1 className="text-2xl font-bold text-gray-900">Track Your Complaint</h1>
          <p className="text-gray-500 text-sm mt-1">Enter your case number to view the full status and history.</p>
        </div>

        {/* Search */}
        <form onSubmit={handleTrack} className="flex gap-3 mb-10">
          <div className="flex-1">
            <Input
              value={caseNumber}
              onChange={e => setCaseNumber(e.target.value)}
              placeholder="e.g. COMPC-ABC123-XYZ"
              className="font-mono"
            />
          </div>
          <Button type="submit" loading={loading}>
            <Search size={15} /> Search
          </Button>
        </form>

        {/* Not found */}
        {notFound && (
          <div className="flex items-center gap-3 border border-red-200 bg-red-50 rounded-xl px-4 py-3 text-red-700 text-sm">
            <AlertCircle size={16} className="flex-shrink-0" />
            No complaint found for <strong className="ml-1">{caseNumber}</strong>. Please check the case number and try again.
          </div>
        )}

        {/* Result */}
        {complaint && (
          <div className="animate-fadeIn">

            {/* Case header */}
            <div className="flex items-start justify-between mb-8 pb-6 border-b border-gray-100">
              <div>
                <p className="text-xs text-gray-400 uppercase tracking-widest mb-1">Case Number</p>
                <p className="text-xl font-bold font-mono text-gray-900">{complaint.case_number}</p>
                <p className="text-xs text-gray-400 mt-1">Submitted {formatDate(complaint.created_at)}</p>
              </div>
              <span className={`text-xs font-semibold px-3 py-1.5 rounded-full border ${status.bg} ${status.text}`}>
                {status.label}
              </span>
            </div>

            {/* Current stage pill */}
            {complaint.stages && (
              <div className="flex items-center gap-2 mb-8 text-sm text-gray-500">
                <Clock size={14} />
                Current stage:
                <span className="font-medium text-gray-900">{complaint.stages.name}</span>
              </div>
            )}

            {/* Email-thread timeline */}
            <div className="flex flex-col">
              {timeline.map((event, idx) => {
                const Icon = event.icon
                const isLast = idx === timeline.length - 1
                return (
                  <div key={event.id} className="flex gap-4">
                    {/* Left: dot + line */}
                    <div className="flex flex-col items-center flex-shrink-0">
                      <div className={`w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0 ${event.dotClass}`} />
                      {!isLast && <div className="w-px flex-1 bg-gray-100 mt-1 mb-0" />}
                    </div>

                    {/* Right: content */}
                    <div className={`flex-1 ${!isLast ? 'pb-8' : 'pb-2'}`}>
                      <div className="flex items-center gap-2 mb-1">
                        <p className="text-sm font-semibold text-gray-900">{event.title}</p>
                      </div>
                      <p className="text-xs text-gray-400 mb-2">{formatDateTime(event.date)}</p>
                      {event.body && (
                        <p className="text-sm text-gray-600 leading-relaxed bg-gray-50 border border-gray-100 rounded-xl px-4 py-3">
                          {event.type === 'submitted'
                            ? event.body.length > 200
                              ? event.body.slice(0, 200) + '…'
                              : event.body
                            : event.body}
                        </p>
                      )}
                    </div>
                  </div>
                )
              })}
            </div>

            {/* Footer note */}
            <div className="mt-8 pt-6 border-t border-gray-100 text-xs text-gray-400">
              Last updated {formatDateTime(complaint.updated_at || complaint.created_at)}
            </div>
          </div>
        )}
      </div>
    </PublicLayout>
  )
}
