import { useEffect, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import PortalLayout from '@/components/layout/PortalLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import Textarea from '@/components/ui/Textarea'
import { supabase } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import { formatDate, formatDateTime, getStatusLabel } from '@/lib/utils'
import { ArrowLeft, Paperclip, Send } from 'lucide-react'
import toast from 'react-hot-toast'

const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

export default function LawyerComplaintDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { getLawyerSession, clearLawyerSession } = useAuthStore()
  const session = getLawyerSession()
  const [complaint, setComplaint] = useState(null)
  const [assignment, setAssignment] = useState(null)
  const [attachments, setAttachments] = useState([])
  const [loading, setLoading] = useState(true)
  const [inputText, setInputText] = useState('')
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    if (!session) { navigate('/lawyer/login'); return }
    load()
  }, [id])

  const load = async () => {
    setLoading(true)
    const [{ data: c }, { data: cl }, { data: att }] = await Promise.all([
      supabase.from('complaints').select('*, stages(*)').eq('id', id).single(),
      supabase.from('complaint_lawyers').select('*').eq('complaint_id', id).eq('user_id', session.userId).single(),
      supabase.from('attachments').select('*').eq('complaint_id', id),
    ])
    setComplaint(c)
    setAssignment(cl)
    setInputText(cl?.input || '')
    setAttachments(att || [])
    setLoading(false)
  }

  const submitInput = async () => {
    if (!inputText.trim()) return toast.error('Input cannot be empty')
    setSaving(true)
    try {
      await supabase.from('complaint_lawyers').update({ input: inputText, responded_at: new Date().toISOString() }).eq('id', assignment.id)
      toast.success('Legal input submitted')
      load()
    } catch {
      toast.error('Failed to submit')
    } finally {
      setSaving(false)
    }
  }

  const handleSignOut = async () => {
    clearLawyerSession()
    await supabase.auth.signOut()
    navigate('/lawyer/login')
  }

  if (loading) return (
    <PortalLayout title="Lawyer Portal" role="lawyer" onSignOut={handleSignOut} userName={session?.profile?.name}>
      <div className="p-10 text-center text-gray-400">Loading...</div>
    </PortalLayout>
  )

  return (
    <PortalLayout title="Lawyer Portal" role="lawyer" onSignOut={handleSignOut} userName={session?.profile?.name}>
      <div className="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="sm" onClick={() => navigate('/lawyer/dashboard')}><ArrowLeft size={15}/> Back</Button>
        <div className="flex-1">
          <h2 className="text-xl font-bold font-mono text-gray-900">{complaint?.case_number}</h2>
          <p className="text-xs text-gray-400">{formatDateTime(complaint?.created_at)}</p>
        </div>
        <Badge variant={statusVariant[complaint?.status] || 'default'}>{getStatusLabel(complaint?.status)}</Badge>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div className="lg:col-span-2 flex flex-col gap-5">
          <Card>
            <CardHeader><CardTitle>Case Details</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-3 text-sm">
              <p className="text-gray-800 leading-relaxed">{complaint?.description}</p>
              <div className="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                {complaint?.complaint_type && <div><p className="text-xs text-gray-400">Type</p><p className="capitalize">{complaint.complaint_type.replace('_',' ')}</p></div>}
                {complaint?.date_of_experience && <div><p className="text-xs text-gray-400">Date</p><p>{formatDate(complaint.date_of_experience)}</p></div>}
                {complaint?.complainee_name && <div><p className="text-xs text-gray-400">Respondent Party</p><p>{complaint.complainee_name}</p></div>}
                {complaint?.evidence_type && complaint.evidence_type !== 'none' && <div><p className="text-xs text-gray-400">Evidence</p><p className="capitalize">{complaint.evidence_type}</p></div>}
              </div>
              {complaint?.witnesses && <div><p className="text-xs text-gray-400">Witnesses</p><p className="text-gray-700">{complaint.witnesses}</p></div>}
              {complaint?.evidence_description && <div><p className="text-xs text-gray-400">Evidence Description</p><p className="text-gray-700">{complaint.evidence_description}</p></div>}
            </CardContent>
          </Card>

          {/* Legal Input */}
          <Card>
            <CardHeader><CardTitle>Legal Input / Opinion</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-3">
              {assignment?.responded_at ? (
                <div className="bg-purple-50 border border-purple-100 rounded-lg p-4">
                  <p className="text-xs text-purple-400 mb-2">Submitted {formatDateTime(assignment.responded_at)}</p>
                  <p className="text-sm text-gray-800">{assignment.input}</p>
                </div>
              ) : (
                <>
                  <Textarea
                    value={inputText}
                    onChange={e => setInputText(e.target.value)}
                    placeholder="Provide your legal assessment, opinion, and recommendations..."
                    rows={8}
                  />
                  <div className="flex justify-end">
                    <Button onClick={submitInput} loading={saving} className="flex items-center gap-1.5">
                      <Send size={14} /> Submit Legal Input
                    </Button>
                  </div>
                </>
              )}
            </CardContent>
          </Card>

          {attachments.length > 0 && (
            <Card>
              <CardHeader><CardTitle>Case Documents</CardTitle></CardHeader>
              <CardContent className="flex flex-col gap-2">
                {attachments.map(att => (
                  <a key={att.id} href={att.file_path} target="_blank" rel="noreferrer"
                    className="flex items-center gap-2 text-sm text-blue-700 hover:underline p-2 border border-gray-100 rounded-lg">
                    <Paperclip size={13} />
                    <span>{att.file_name || 'Document'}</span>
                  </a>
                ))}
              </CardContent>
            </Card>
          )}
        </div>

        <div>
          <Card>
            <CardHeader><CardTitle>Case Info</CardTitle></CardHeader>
            <CardContent className="text-sm flex flex-col gap-3">
              <div>
                <p className="text-xs text-gray-400">Status</p>
                <Badge variant={statusVariant[complaint?.status] || 'default'} className="mt-1">{getStatusLabel(complaint?.status)}</Badge>
              </div>
              {complaint?.stages && <div><p className="text-xs text-gray-400">Stage</p><p>{complaint.stages.name}</p></div>}
              <div>
                <p className="text-xs text-gray-400">My Assignment</p>
                <p className={assignment?.responded_at ? 'text-green-600' : 'text-yellow-600'}>
                  {assignment?.responded_at ? '✓ Input submitted' : '⏳ Pending input'}
                </p>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </PortalLayout>
  )
}
