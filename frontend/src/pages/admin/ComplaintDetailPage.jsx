import { useEffect, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import Select from '@/components/ui/Select'
import Textarea from '@/components/ui/Textarea'
import Modal from '@/components/ui/Modal'
import { supabase } from '@/lib/supabase'
import { formatDate, formatDateTime, getStatusLabel } from '@/lib/utils'
import { ArrowLeft, User, Calendar, MapPin, FileText, Plus, Trash2, Scroll, Paperclip } from 'lucide-react'
import { useAuthStore } from '@/store/authStore'
import toast from 'react-hot-toast'

const STATUSES = ['submitted','under_review','escalated','resolved','closed']
const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

export default function ComplaintDetailPage() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [complaint, setComplaint] = useState(null)
  const [stages, setStages] = useState([])
  const [respondents, setRespondents] = useState([])
  const [lawyers, setLawyers] = useState([])
  const [allUsers, setAllUsers] = useState([])
  const [logs, setLogs] = useState([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [showLogModal, setShowLogModal] = useState(false)
  const [logForm, setLogForm] = useState({ note: '', next_steps: '' })
  const [attachments, setAttachments] = useState([])
  const [uploading, setUploading] = useState(false)
  const [showAssignModal, setShowAssignModal] = useState(null) // 'respondent' | 'lawyer'
  const [assignUserId, setAssignUserId] = useState('')
  const { user } = useAuthStore()

  const load = async () => {
    setLoading(true)
    try {
      const [{ data: c }, { data: s }, { data: cr }, { data: cl }, { data: il }, { data: u }, { data: att }] = await Promise.all([
        supabase.from('complaints').select('*, stages(*), locations(*)').eq('id', id).single(),
        supabase.from('stages').select('*').eq('is_active', true).order('step_number'),
        supabase.from('complaint_respondents').select('*, profiles(*)').eq('complaint_id', id),
        supabase.from('complaint_lawyers').select('*, profiles(*)').eq('complaint_id', id),
        supabase.from('investigation_logs').select('*, profiles(name)').eq('complaint_id', id).order('created_at', { ascending: false }),
        supabase.from('profiles').select('id,name,email,role').in('role', ['respondent','lawyer']),
        supabase.from('attachments').select('*').eq('complaint_id', id).order('created_at', { ascending: false }),
      ])
      setComplaint(c)
      setStages(s || [])
      setRespondents(cr || [])
      setLawyers(cl || [])
      setLogs(il || [])
      setAllUsers(u || [])
      setAttachments(att || [])
    } catch (err) {
      toast.error('Failed to load complaint details')
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => { load() }, [id])

  const updateStatus = async (status) => {
    setSaving(true)
    const { error } = await supabase.from('complaints').update({ status, updated_at: new Date().toISOString() }).eq('id', id)
    if (error) toast.error('Failed to update status')
    else { toast.success('Status updated'); setComplaint(c => ({ ...c, status })) }
    setSaving(false)
  }

  const updateStage = async (stage_id) => {
    const oldStageId = complaint.stage_id
    const { error } = await supabase.from('complaints').update({ stage_id, updated_at: new Date().toISOString() }).eq('id', id)
    if (!error) {
      await supabase.from('stage_change_logs').insert({ complaint_id: id, from_stage_id: oldStageId, to_stage_id: stage_id })
      toast.success('Stage updated')
      load()
    } else toast.error('Failed to update stage')
  }

  const addLog = async () => {
    if (!logForm.note.trim()) return toast.error('Note is required')
    const { data: { user } } = await supabase.auth.getUser()
    const { error } = await supabase.from('investigation_logs').insert({
      complaint_id: id, note: logForm.note, next_steps: logForm.next_steps, created_by: user?.id
    })
    if (error) toast.error('Failed to add log')
    else { toast.success('Log added'); setShowLogModal(false); setLogForm({ note:'', next_steps:'' }); load() }
  }

  const deleteLog = async (logId) => {
    if (!confirm('Delete this log?')) return
    await supabase.from('investigation_logs').delete().eq('id', logId)
    toast.success('Log deleted')
    load()
  }

  const assign = async () => {
    if (!assignUserId) return toast.error('Select a user')
    const table = showAssignModal === 'respondent' ? 'complaint_respondents' : 'complaint_lawyers'
    const { error } = await supabase.from(table).insert({ complaint_id: id, user_id: assignUserId })
    if (error) toast.error(error.message || 'Failed to assign')
    else { toast.success('Assigned successfully'); setShowAssignModal(null); setAssignUserId(''); load() }
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
      toast.error('Upload failed: ' + (err.message || 'Unknown error'))
    } finally {
      setUploading(false)
      e.target.value = ''
    }
  }

  const removeAssignment = async (table, assignId) => {
    if (!confirm('Remove this assignment?')) return
    await supabase.from(table).delete().eq('id', assignId)
    toast.success('Removed'); load()
  }

  if (loading) return <AdminLayout><div className="p-10 text-center text-gray-400">Loading...</div></AdminLayout>
  if (!complaint) return <AdminLayout><div className="p-10 text-center text-gray-400">Complaint not found</div></AdminLayout>

  const availableForRole = (role) => allUsers.filter(u => u.role === role)

  return (
    <AdminLayout>
      <div className="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/complaints')}>
          <ArrowLeft size={16} /> Back
        </Button>
        <div className="flex-grow" />
        <div className="flex-1">
          <h1 className="text-xl font-bold text-gray-900 font-mono">{complaint.case_number}</h1>
          <p className="text-gray-500 text-xs mt-0.5">Submitted {formatDateTime(complaint.created_at)}</p>
        </div>
        <Button variant="outline" size="sm" onClick={() => navigate(`/admin/complaints/${id}/resolution`)}>
          <Scroll size={14} /> Resolution
        </Button>
        <Badge variant={statusVariant[complaint.status] || 'default'}>{getStatusLabel(complaint.status)}</Badge>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {/* Main column */}
        <div className="lg:col-span-2 flex flex-col gap-5">
          {/* Description */}
          <Card>
            <CardHeader><CardTitle>Complaint Details</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-3 text-sm">
              <div>
                <p className="text-xs text-gray-400 mb-1">Description</p>
                <p className="text-gray-800">{complaint.description}</p>
              </div>
              <div className="grid grid-cols-2 gap-3">
                {complaint.complaint_type && (
                  <div>
                    <p className="text-xs text-gray-400">Type</p>
                    <p className="text-gray-800 capitalize">{complaint.complaint_type.replace('_',' ')}</p>
                  </div>
                )}
                {complaint.complaint_about && (
                  <div>
                    <p className="text-xs text-gray-400">About</p>
                    <p className="text-gray-800 capitalize">{complaint.complaint_about}</p>
                  </div>
                )}
                {complaint.date_of_experience && (
                  <div>
                    <p className="text-xs text-gray-400">Date of Experience</p>
                    <p className="text-gray-800">{formatDate(complaint.date_of_experience)}</p>
                  </div>
                )}
                <div>
                  <p className="text-xs text-gray-400">Anonymous</p>
                  <p className="text-gray-800">{complaint.anonymity ? 'Yes' : 'No'}</p>
                </div>
              </div>
              {complaint.evidence_type && complaint.evidence_type !== 'none' && (
                <div>
                  <p className="text-xs text-gray-400">Evidence Type</p>
                  <p className="text-gray-800 capitalize">{complaint.evidence_type}</p>
                  {complaint.evidence_description && <p className="text-gray-500 text-xs mt-0.5">{complaint.evidence_description}</p>}
                </div>
              )}
              {complaint.witnesses && (
                <div>
                  <p className="text-xs text-gray-400">Witnesses</p>
                  <p className="text-gray-800">{complaint.witnesses}</p>
                </div>
              )}
            </CardContent>
          </Card>

          {/* Person complained about */}
          {(complaint.complainee_name || complaint.complainee_email) && (
            <Card>
              <CardHeader><CardTitle>Person Complained About</CardTitle></CardHeader>
              <CardContent className="grid grid-cols-2 gap-3 text-sm">
                {complaint.complainee_name && <div><p className="text-xs text-gray-400">Name</p><p className="text-gray-800">{complaint.complainee_name}</p></div>}
                {complaint.complainee_email && <div><p className="text-xs text-gray-400">Email</p><p className="text-gray-800">{complaint.complainee_email}</p></div>}
                {complaint.complainee_address && <div className="col-span-2"><p className="text-xs text-gray-400">Address</p><p className="text-gray-800">{complaint.complainee_address}</p></div>}
              </CardContent>
            </Card>
          )}

          {/* Investigation Logs */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle>Investigation Logs</CardTitle>
                <Button size="sm" onClick={() => setShowLogModal(true)}><Plus size={14} /> Add Log</Button>
              </div>
            </CardHeader>
            <CardContent className="flex flex-col gap-3">
              {logs.length === 0 ? (
                <p className="text-sm text-gray-400 py-2">No investigation logs yet</p>
              ) : logs.map(log => (
                <div key={log.id} className="border border-gray-100 rounded-lg p-3">
                  <div className="flex items-start justify-between gap-2">
                    <div className="flex-1">
                      <p className="text-sm text-gray-800">{log.note}</p>
                      {log.next_steps && <p className="text-xs text-gray-500 mt-1"><strong>Next steps:</strong> {log.next_steps}</p>}
                      <p className="text-xs text-gray-400 mt-1">{log.profiles?.name} · {formatDateTime(log.created_at)}</p>
                    </div>
                    <Button variant="ghost" size="sm" onClick={() => deleteLog(log.id)}>
                      <Trash2 size={13} className="text-red-500" />
                    </Button>
                  </div>
                </div>
              ))}
            </CardContent>
          </Card>

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

        {/* Right column */}
        <div className="flex flex-col gap-5">
          {/* Complainant info */}
          <Card>
            <CardHeader><CardTitle>Complainant</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-2 text-sm">
              {complaint.anonymity ? (
                <p className="text-gray-500 italic">Anonymous submission</p>
              ) : (
                <>
                  {complaint.name && <div className="flex items-center gap-2 text-gray-700"><User size={14}/>{complaint.name}</div>}
                  {complaint.email && <div className="text-gray-500 text-xs">{complaint.email}</div>}
                  {complaint.phone_number && <div className="text-gray-500 text-xs">{complaint.phone_number}</div>}
                </>
              )}
            </CardContent>
          </Card>

          {/* Status & Stage */}
          <Card>
            <CardHeader><CardTitle>Status & Stage</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-3">
              <Select label="Status" value={complaint.status} onChange={e => updateStatus(e.target.value)} disabled={saving}>
                {STATUSES.map(s => <option key={s} value={s}>{getStatusLabel(s)}</option>)}
              </Select>
              <Select label="Stage" value={complaint.stage_id || ''} onChange={e => updateStage(e.target.value)} disabled={saving}>
                <option value="">No stage</option>
                {stages.map(s => <option key={s.id} value={s.id}>{s.step_number}. {s.name}</option>)}
              </Select>
            </CardContent>
          </Card>

          {/* Respondents */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle>Respondents</CardTitle>
                <Button size="sm" variant="outline" onClick={() => setShowAssignModal('respondent')}><Plus size={14}/></Button>
              </div>
            </CardHeader>
            <CardContent className="flex flex-col gap-2">
              {respondents.length === 0 ? (
                <p className="text-sm text-gray-400">None assigned</p>
              ) : respondents.map(r => (
                <div key={r.id} className="flex items-center justify-between text-sm">
                  <span className="text-gray-700">{r.profiles?.name}</span>
                  <Button variant="ghost" size="sm" onClick={() => removeAssignment('complaint_respondents', r.id)}>
                    <Trash2 size={13} className="text-red-500" />
                  </Button>
                </div>
              ))}
            </CardContent>
          </Card>

          {/* Lawyers */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle>Lawyers</CardTitle>
                <Button size="sm" variant="outline" onClick={() => setShowAssignModal('lawyer')}><Plus size={14}/></Button>
              </div>
            </CardHeader>
            <CardContent className="flex flex-col gap-2">
              {lawyers.length === 0 ? (
                <p className="text-sm text-gray-400">None assigned</p>
              ) : lawyers.map(l => (
                <div key={l.id} className="flex items-center justify-between text-sm">
                  <span className="text-gray-700">{l.profiles?.name}</span>
                  <Button variant="ghost" size="sm" onClick={() => removeAssignment('complaint_lawyers', l.id)}>
                    <Trash2 size={13} className="text-red-500" />
                  </Button>
                </div>
              ))}
            </CardContent>
          </Card>
        </div>
      </div>

      {/* Add Log Modal */}
      <Modal open={showLogModal} onClose={() => setShowLogModal(false)} title="Add Investigation Log">
        <div className="flex flex-col gap-4">
          <Textarea label="Note *" value={logForm.note} onChange={e => setLogForm(f=>({...f,note:e.target.value}))} placeholder="Describe findings..." rows={4} />
          <Textarea label="Next Steps" value={logForm.next_steps} onChange={e => setLogForm(f=>({...f,next_steps:e.target.value}))} placeholder="What needs to happen next..." rows={3} />
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => setShowLogModal(false)}>Cancel</Button>
            <Button onClick={addLog}>Add Log</Button>
          </div>
        </div>
      </Modal>

      {/* Assign Modal */}
      <Modal
        open={!!showAssignModal}
        onClose={() => { setShowAssignModal(null); setAssignUserId('') }}
        title={`Assign ${showAssignModal === 'respondent' ? 'Respondent' : 'Lawyer'}`}
      >
        <div className="flex flex-col gap-4">
          <Select
            label={`Select ${showAssignModal}`}
            value={assignUserId}
            onChange={e => setAssignUserId(e.target.value)}
          >
            <option value="">Choose...</option>
            {availableForRole(showAssignModal).map(u => (
              <option key={u.id} value={u.id}>{u.name} ({u.email})</option>
            ))}
          </Select>
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => { setShowAssignModal(null); setAssignUserId('') }}>Cancel</Button>
            <Button onClick={assign}>Assign</Button>
          </div>
        </div>
      </Modal>
    </AdminLayout>
  )
}
