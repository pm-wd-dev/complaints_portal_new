import { useEffect, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import PortalLayout from '@/components/layout/PortalLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import { supabase } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import { formatDate, formatDateTime, getStatusLabel } from '@/lib/utils'
import { ArrowLeft, User, Paperclip } from 'lucide-react'
import toast from 'react-hot-toast'

const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

export default function CastMemberComplaintDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { user, profile, signOut } = useAuthStore()
  const [complaint, setComplaint] = useState(null)
  const [respondents, setRespondents] = useState([])
  const [responses, setResponses] = useState([])
  const [logs, setLogs] = useState([])
  const [attachments, setAttachments] = useState([])
  const [loading, setLoading] = useState(true)
  const [uploading, setUploading] = useState(false)

  useEffect(() => { load() }, [id])

  const load = async () => {
    setLoading(true)
    const [{ data: c }, { data: cr }, { data: il }, { data: att }] = await Promise.all([
      supabase.from('complaints').select('*, stages(*), locations(*)').eq('id', id).single(),
      supabase.from('complaint_respondents').select('*, profiles(name,email), complaint_responses(*)').eq('complaint_id', id),
      supabase.from('investigation_logs').select('*, profiles(name)').eq('complaint_id', id).order('created_at', { ascending: false }),
      supabase.from('attachments').select('*').eq('complaint_id', id).order('created_at', { ascending: false }),
    ])
    setComplaint(c)
    setRespondents(cr || [])
    setLogs(il || [])
    setAttachments(att || [])
    setLoading(false)
  }

  const handleFileUpload = async (e) => {
    const file = e.target.files[0]
    if (!file) return
    if (file.size > 50 * 1024 * 1024) return toast.error('File too large (max 50MB)')
    setUploading(true)
    try {
      const ext = file.name.split('.').pop()
      const path = `complaints/${id}/${Date.now()}.${ext}`
      const { error: uploadError } = await supabase.storage.from('complaint-attachments').upload(path, file)
      if (uploadError) throw uploadError
      const { data: { publicUrl } } = supabase.storage.from('complaint-attachments').getPublicUrl(path)
      await supabase.from('attachments').insert({
        complaint_id: id, file_path: publicUrl, file_name: file.name,
        file_type: file.type, file_size: file.size, uploaded_by: user?.id, type: 'evidence'
      })
      toast.success('File uploaded')
      load()
    } catch (err) {
      toast.error(err.message || 'Upload failed')
    } finally {
      setUploading(false)
      e.target.value = ''
    }
  }

  if (loading) return (
    <PortalLayout title="Cast Member Portal" role="cast_member" onSignOut={async () => { await signOut(); navigate('/cast-member/login') }} userName={profile?.name}>
      <div className="p-10 text-center text-gray-400">Loading...</div>
    </PortalLayout>
  )

  if (!complaint) return (
    <PortalLayout title="Cast Member Portal" role="cast_member" onSignOut={async () => { await signOut(); navigate('/cast-member/login') }} userName={profile?.name}>
      <div className="p-10 text-center text-gray-400">Complaint not found</div>
    </PortalLayout>
  )

  return (
    <PortalLayout title="Cast Member Portal" role="cast_member" onSignOut={async () => { await signOut(); navigate('/cast-member/login') }} userName={profile?.name}>
      <div className="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="sm" onClick={() => navigate('/cast-member/dashboard')}><ArrowLeft size={15}/> Back</Button>
        <div className="flex-1">
          <h2 className="text-xl font-bold text-gray-900 font-mono">{complaint.case_number}</h2>
          <p className="text-xs text-gray-400">Submitted {formatDateTime(complaint.created_at)}</p>
        </div>
        <Badge variant={statusVariant[complaint.status] || 'default'}>{getStatusLabel(complaint.status)}</Badge>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div className="lg:col-span-2 flex flex-col gap-5">
          {/* Details */}
          <Card>
            <CardHeader><CardTitle>Complaint Details</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-3 text-sm">
              <p className="text-gray-800">{complaint.description}</p>
              <div className="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
                {complaint.complaint_type && <div><p className="text-xs text-gray-400">Type</p><p className="text-gray-700 capitalize">{complaint.complaint_type.replace('_',' ')}</p></div>}
                {complaint.date_of_experience && <div><p className="text-xs text-gray-400">Date of Experience</p><p className="text-gray-700">{formatDate(complaint.date_of_experience)}</p></div>}
                {complaint.stages && <div><p className="text-xs text-gray-400">Stage</p><p className="text-gray-700">{complaint.stages.name}</p></div>}
                {complaint.complainee_name && <div><p className="text-xs text-gray-400">Complained About</p><p className="text-gray-700">{complaint.complainee_name}</p></div>}
              </div>
              {complaint.witnesses && <div><p className="text-xs text-gray-400">Witnesses</p><p className="text-gray-700">{complaint.witnesses}</p></div>}
            </CardContent>
          </Card>

          {/* Respondent Responses */}
          {respondents.length > 0 && (
            <Card>
              <CardHeader><CardTitle>Respondent Responses</CardTitle></CardHeader>
              <CardContent className="flex flex-col gap-4">
                {respondents.map(r => (
                  <div key={r.id} className="border border-gray-100 rounded-lg p-3">
                    <p className="text-xs text-gray-400 mb-1">{r.profiles?.name} · {r.responded_at ? formatDateTime(r.responded_at) : 'Pending'}</p>
                    {r.complaint_responses?.map(resp => (
                      <p key={resp.id} className="text-sm text-gray-700">{resp.response}</p>
                    ))}
                    {!r.responded_at && <p className="text-xs text-yellow-600 italic">Awaiting response...</p>}
                  </div>
                ))}
              </CardContent>
            </Card>
          )}

          {/* Investigation Logs */}
          {logs.length > 0 && (
            <Card>
              <CardHeader><CardTitle>Investigation Notes</CardTitle></CardHeader>
              <CardContent className="flex flex-col gap-3">
                {logs.map(log => (
                  <div key={log.id} className="border border-gray-100 rounded-lg p-3">
                    <p className="text-sm text-gray-800">{log.note}</p>
                    {log.next_steps && <p className="text-xs text-gray-500 mt-1"><strong>Next:</strong> {log.next_steps}</p>}
                    <p className="text-xs text-gray-400 mt-1">{log.profiles?.name} · {formatDateTime(log.created_at)}</p>
                  </div>
                ))}
              </CardContent>
            </Card>
          )}

          {/* Attachments */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle>Attachments</CardTitle>
                <label className={`cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-blue-700 text-white hover:bg-blue-800 transition-colors ${uploading ? 'opacity-60 cursor-not-allowed' : ''}`}>
                  <Paperclip size={13} /> {uploading ? 'Uploading...' : 'Upload File'}
                  <input type="file" className="hidden" onChange={handleFileUpload} disabled={uploading} accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.mp4,.mov" />
                </label>
              </div>
            </CardHeader>
            <CardContent>
              {attachments.length === 0 ? (
                <p className="text-sm text-gray-400">No attachments yet</p>
              ) : (
                <div className="flex flex-col gap-2">
                  {attachments.map(att => (
                    <a key={att.id} href={att.file_path} target="_blank" rel="noreferrer"
                      className="flex items-center gap-2 text-sm text-blue-700 hover:underline p-2 border border-gray-100 rounded-lg hover:bg-gray-50">
                      <Paperclip size={13} />
                      <span className="flex-1 truncate">{att.file_name || 'Attachment'}</span>
                      <span className="text-xs text-gray-400">{att.file_size ? `${(att.file_size / 1024).toFixed(0)} KB` : ''}</span>
                    </a>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </div>

        {/* Right sidebar */}
        <div className="flex flex-col gap-5">
          <Card>
            <CardHeader><CardTitle>Complainant</CardTitle></CardHeader>
            <CardContent className="text-sm flex flex-col gap-1">
              {complaint.anonymity ? <p className="text-gray-400 italic">Anonymous</p> : (
                <>
                  {complaint.name && <p className="text-gray-800 font-medium">{complaint.name}</p>}
                  {complaint.email && <p className="text-gray-500">{complaint.email}</p>}
                  {complaint.phone_number && <p className="text-gray-500">{complaint.phone_number}</p>}
                </>
              )}
            </CardContent>
          </Card>

          {(complaint.complainee_name || complaint.complainee_email) && (
            <Card>
              <CardHeader><CardTitle>Complained About</CardTitle></CardHeader>
              <CardContent className="text-sm flex flex-col gap-1">
                {complaint.complainee_name && <p className="text-gray-800 font-medium">{complaint.complainee_name}</p>}
                {complaint.complainee_email && <p className="text-gray-500">{complaint.complainee_email}</p>}
                {complaint.complainee_address && <p className="text-gray-500">{complaint.complainee_address}</p>}
              </CardContent>
            </Card>
          )}

          <Card>
            <CardHeader><CardTitle>Assigned Respondents</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-2 text-sm">
              {respondents.length === 0 ? (
                <p className="text-gray-400">None assigned yet</p>
              ) : respondents.map(r => (
                <div key={r.id} className="flex items-center justify-between">
                  <span className="text-gray-700">{r.profiles?.name}</span>
                  <span className={`text-xs ${r.responded_at ? 'text-green-600' : 'text-yellow-600'}`}>
                    {r.responded_at ? '✓ Responded' : 'Pending'}
                  </span>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>
      </div>
    </PortalLayout>
  )
}
