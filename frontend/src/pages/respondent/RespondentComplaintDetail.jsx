import { useEffect, useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import PortalLayout from '@/components/layout/PortalLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Textarea from '@/components/ui/Textarea'
import { supabase, getErrorMessage } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import { formatDate, formatDateTime, getStatusLabel } from '@/lib/utils'
import { ArrowLeft, Upload, CheckCircle, Paperclip } from 'lucide-react'
import toast from 'react-hot-toast'

const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

const EVIDENCE_OPTIONS = [
  { value: 'photos',    label: '📸 Photos/Screenshots' },
  { value: 'videos',    label: '🎥 Videos' },
  { value: 'messages',  label: '📧 Messages/Emails' },
  { value: 'documents', label: '📝 Other Documents' },
  { value: 'none',      label: '❌ No supporting evidence' },
]

export default function RespondentComplaintDetail() {
  const { id } = useParams()
  const navigate = useNavigate()
  const { getRespondentSession, clearRespondentSession } = useAuthStore()
  const session = getRespondentSession()

  const [complaint, setComplaint]   = useState(null)
  const [assignment, setAssignment] = useState(null)
  const [attachments, setAttachments] = useState([])
  const [loading, setLoading]       = useState(true)
  const [saving, setSaving]         = useState(false)
  const [evidenceFile, setEvidenceFile] = useState(null)

  const [form, setForm] = useState({
    venueName: '',
    venueCityState: '',
    sideOfStory: '',
    issueDescription: '',
    witnesses: '',
    evidenceTypes: [],
    evidenceDescription: '',
  })

  const set = (k, v) => setForm(f => ({ ...f, [k]: v }))

  const toggleEvidence = (value) => {
    setForm(f => {
      if (value === 'none') {
        return { ...f, evidenceTypes: f.evidenceTypes.includes('none') ? [] : ['none'] }
      }
      const without = f.evidenceTypes.filter(v => v !== 'none')
      const updated = without.includes(value)
        ? without.filter(v => v !== value)
        : [...without, value]
      return { ...f, evidenceTypes: updated }
    })
  }

  useEffect(() => {
    if (!session) { navigate('/login'); return }
    load()
  }, [id])

  const load = async () => {
    setLoading(true)
    try {
      const [{ data: c }, { data: cr }, { data: att }] = await Promise.all([
        supabase.from('complaints').select('*, stages(*)').eq('id', id).single(),
        supabase.from('complaint_respondents').select('*, complaint_responses(*)')
          .eq('complaint_id', id).eq('user_id', session.userId).single(),
        supabase.from('attachments').select('*').eq('complaint_id', id),
      ])
      setComplaint(c)
      setAssignment(cr)
      setAttachments(att || [])

      // Pre-fill form if already responded
      if (cr?.input) {
        try {
          const saved = JSON.parse(cr.input)
          setForm(f => ({ ...f, ...saved }))
        } catch { /* input not JSON, ignore */ }
      }
    } catch (err) {
      toast.error('Failed to load complaint')
    } finally {
      setLoading(false)
    }
  }

  const submitResponse = async (e) => {
    e.preventDefault()
    if (!form.venueName.trim()) return toast.error('Legal name of venue is required')
    if (!form.venueCityState.trim()) return toast.error('City and state of venue is required')
    if (!form.sideOfStory.trim()) return toast.error('Your side of the story is required')
    if (!form.issueDescription.trim()) return toast.error('Issue description is required')
    if (!form.witnesses.trim()) return toast.error('Please fill in the witnesses field')
    if (form.evidenceTypes.length === 0) return toast.error('Please select at least one evidence option')
    if (!assignment) return toast.error('Not assigned to this complaint')

    setSaving(true)
    try {
      const inputJson = JSON.stringify(form)
      const formattedResponse = [
        `Venue: ${form.venueName} — ${form.venueCityState}`,
        `\nSide of Story:\n${form.sideOfStory}`,
        `\nIssue Description:\n${form.issueDescription}`,
        `\nWitnesses: ${form.witnesses}`,
        `\nEvidence: ${form.evidenceTypes.join(', ')}`,
        form.evidenceDescription ? `\nEvidence Description: ${form.evidenceDescription}` : '',
      ].filter(Boolean).join('\n')

      await supabase.from('complaint_responses').insert({
        complaint_respondent_id: assignment.id,
        response: formattedResponse,
      })
      await supabase.from('complaint_respondents').update({
        responded_at: new Date().toISOString(),
        input: inputJson,
      }).eq('id', assignment.id)

      // Upload evidence file if provided
      if (evidenceFile) {
        const ext = evidenceFile.name.split('.').pop()
        const path = `complaints/${id}/respondent-${Date.now()}.${ext}`
        const { error: upErr } = await supabase.storage
          .from('complaint-attachments')
          .upload(path, evidenceFile)
        if (!upErr) {
          const { data: { publicUrl } } = supabase.storage
            .from('complaint-attachments')
            .getPublicUrl(path)
          await supabase.from('attachments').insert({
            complaint_id: id,
            file_path: publicUrl,
            file_name: evidenceFile.name,
            file_type: evidenceFile.type,
            file_size: evidenceFile.size,
            uploaded_by: session.userId,
            type: 'response',
          })
        }
      }

      toast.success('Response submitted successfully')
      load()
    } catch (err) {
      toast.error(getErrorMessage(err))
    } finally {
      setSaving(false)
    }
  }

  const handleSignOut = async () => {
    clearRespondentSession()
    await supabase.auth.signOut()
    navigate('/login')
  }

  if (loading) return (
    <PortalLayout title="Respondent Portal" role="respondent" onSignOut={handleSignOut} userName={session?.profile?.name}>
      <div className="p-10 text-center text-gray-400">Loading...</div>
    </PortalLayout>
  )

  const hasResponded = !!assignment?.responded_at

  return (
    <PortalLayout title="Respondent Portal" role="respondent" onSignOut={handleSignOut} userName={session?.profile?.name}>
      <div className="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="sm" onClick={() => navigate('/respondent/dashboard')}>
          <ArrowLeft size={15} /> Back
        </Button>
        <div className="flex-1">
          <h2 className="text-xl font-bold font-mono text-gray-900">{complaint?.case_number}</h2>
          <p className="text-xs text-gray-400">{formatDateTime(complaint?.created_at)}</p>
        </div>
        <Badge variant={statusVariant[complaint?.status] || 'default'}>{getStatusLabel(complaint?.status)}</Badge>
      </div>

      <div className="max-w-2xl flex flex-col gap-5">

        {/* Complaint summary (read-only) */}
        <Card>
          <CardHeader><CardTitle>Complaint Summary</CardTitle></CardHeader>
          <CardContent className="flex flex-col gap-3 text-sm">
            <p className="text-gray-800 leading-relaxed">{complaint?.description}</p>
            <div className="grid grid-cols-2 gap-3 pt-2 border-t border-gray-100">
              {complaint?.date_of_experience && (
                <div><p className="text-xs text-gray-400">Date</p><p>{formatDate(complaint.date_of_experience)}</p></div>
              )}
              {complaint?.complainee_name && (
                <div><p className="text-xs text-gray-400">Complained About</p><p>{complaint.complainee_name}</p></div>
              )}
            </div>
          </CardContent>
        </Card>

        {hasResponded ? (
          /* Already submitted — show read-only summary */
          <Card>
            <CardHeader>
              <div className="flex items-center gap-2">
                <CheckCircle size={18} className="text-green-500" />
                <CardTitle>Response Submitted</CardTitle>
              </div>
            </CardHeader>
            <CardContent className="flex flex-col gap-3 text-sm">
              <div className="grid grid-cols-2 gap-3">
                <div><p className="text-xs text-gray-400">Venue</p><p>{form.venueName || '—'}</p></div>
                <div><p className="text-xs text-gray-400">Location</p><p>{form.venueCityState || '—'}</p></div>
              </div>
              {form.sideOfStory && <div><p className="text-xs text-gray-400">Your Side of the Story</p><p className="whitespace-pre-line">{form.sideOfStory}</p></div>}
              {form.issueDescription && <div><p className="text-xs text-gray-400">Issue Description</p><p className="whitespace-pre-line">{form.issueDescription}</p></div>}
              {form.witnesses && <div><p className="text-xs text-gray-400">Witnesses</p><p>{form.witnesses}</p></div>}
              {form.evidenceTypes.length > 0 && <div><p className="text-xs text-gray-400">Evidence</p><p>{form.evidenceTypes.join(', ')}</p></div>}
              <p className="text-xs text-green-600 mt-1">Submitted {formatDateTime(assignment.responded_at)}</p>
            </CardContent>
          </Card>
        ) : (
          /* Response form */
          <form onSubmit={submitResponse} className="flex flex-col gap-5">
            <div className="bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
              <p className="text-sm font-semibold text-blue-800 mb-0.5">Step 2 — Respondent Response Form</p>
              <p className="text-xs text-blue-600">Please provide your side of the story. All fields marked * are required.</p>
            </div>

            {/* Email */}
            <Card>
              <CardContent className="pt-5 pb-4">
                <label className="block text-sm font-medium text-gray-700 mb-1">Email <span className="text-red-500">*</span></label>
                <div className="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                  {session?.profile?.email || '—'}
                </div>
              </CardContent>
            </Card>

            {/* Case Number */}
            <Card>
              <CardContent className="pt-5 pb-4">
                <label className="block text-sm font-medium text-gray-700 mb-1">Case Number</label>
                <div className="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono text-blue-700">
                  {complaint?.case_number}
                </div>
                <p className="text-xs text-gray-400 mt-1">Case number given by leadership</p>
              </CardContent>
            </Card>

            {/* Venue */}
            <Card>
              <CardContent className="pt-5 pb-4 flex flex-col gap-4">
                <Input
                  label="Legal Name of the Venue *"
                  value={form.venueName}
                  onChange={e => set('venueName', e.target.value)}
                  placeholder="e.g. The Grand Theatre"
                  required
                />
                <Input
                  label="City and State of the Venue *"
                  value={form.venueCityState}
                  onChange={e => set('venueCityState', e.target.value)}
                  placeholder="e.g. Los Angeles, CA"
                  required
                />
              </CardContent>
            </Card>

            {/* Your Name */}
            <Card>
              <CardContent className="pt-5 pb-4">
                <label className="block text-sm font-medium text-gray-700 mb-1">Your Name — Who is responding to this complaint? <span className="text-red-500">*</span></label>
                <div className="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                  {session?.profile?.name || '—'}
                </div>
              </CardContent>
            </Card>

            {/* Date of Complaint */}
            <Card>
              <CardContent className="pt-5 pb-4">
                <label className="block text-sm font-medium text-gray-700 mb-1">Date of Complaint <span className="text-red-500">*</span></label>
                <div className="px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
                  {complaint?.date_of_experience ? formatDate(complaint.date_of_experience) : formatDate(complaint?.created_at)}
                </div>
              </CardContent>
            </Card>

            {/* Side of story */}
            <Card>
              <CardContent className="pt-5 pb-4">
                <Textarea
                  label="Describe your side of the story — What happened from your perspective? *"
                  value={form.sideOfStory}
                  onChange={e => set('sideOfStory', e.target.value)}
                  placeholder="Describe what happened from your point of view..."
                  rows={5}
                  required
                />
              </CardContent>
            </Card>

            {/* Issue description */}
            <Card>
              <CardContent className="pt-5 pb-4">
                <Textarea
                  label="Describe the issue in detail — What happened from your perspective? *"
                  value={form.issueDescription}
                  onChange={e => set('issueDescription', e.target.value)}
                  placeholder="Provide a detailed account including where, when, and who was involved..."
                  rows={5}
                  required
                />
              </CardContent>
            </Card>

            {/* Witnesses */}
            <Card>
              <CardContent className="pt-5 pb-4">
                <Textarea
                  label="Were there any witnesses on your side? — If yes, please list their names and contact information. *"
                  value={form.witnesses}
                  onChange={e => set('witnesses', e.target.value)}
                  placeholder="e.g. John Smith – john@example.com, or 'No witnesses'"
                  rows={3}
                  required
                />
              </CardContent>
            </Card>

            {/* Evidence type checkboxes */}
            <Card>
              <CardHeader><CardTitle>Do you have any supporting evidence? <span className="text-red-500">*</span></CardTitle></CardHeader>
              <CardContent className="flex flex-col gap-2 pt-0">
                {EVIDENCE_OPTIONS.map(opt => (
                  <label key={opt.value} className="flex items-center gap-3 px-3 py-2.5 rounded-lg border border-gray-100 hover:bg-gray-50 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={form.evidenceTypes.includes(opt.value)}
                      onChange={() => toggleEvidence(opt.value)}
                      className="w-4 h-4 rounded accent-blue-600"
                    />
                    <span className="text-sm text-gray-700">{opt.label}</span>
                  </label>
                ))}
              </CardContent>
            </Card>

            {/* Evidence description (conditional) */}
            {form.evidenceTypes.length > 0 && !form.evidenceTypes.includes('none') && (
              <Card>
                <CardContent className="pt-5 pb-4">
                  <Textarea
                    label="If you have evidence, please describe it HERE"
                    value={form.evidenceDescription}
                    onChange={e => set('evidenceDescription', e.target.value)}
                    placeholder="Describe your evidence..."
                    rows={3}
                  />
                </CardContent>
              </Card>
            )}

            {/* File upload (conditional) */}
            {form.evidenceTypes.length > 0 && !form.evidenceTypes.includes('none') && (
              <Card>
                <CardContent className="pt-5 pb-4">
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Please Upload your evidence HERE
                  </label>
                  <div className="border-2 border-dashed border-gray-200 rounded-lg p-4 text-center hover:border-blue-300 transition-colors">
                    <Upload size={20} className="text-gray-400 mx-auto mb-2" />
                    <p className="text-xs text-gray-400 mb-2">Upload 1 supported file. Max 1 GB.</p>
                    <input
                      type="file"
                      onChange={e => setEvidenceFile(e.target.files[0] || null)}
                      className="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer"
                    />
                    {evidenceFile && <p className="text-xs text-green-600 mt-2">Selected: {evidenceFile.name}</p>}
                  </div>
                </CardContent>
              </Card>
            )}

            <div className="flex justify-end pb-6">
              <Button type="submit" loading={saving} size="lg">
                Submit Response
              </Button>
            </div>
          </form>
        )}

        {/* Attachments */}
        {attachments.length > 0 && (
          <Card>
            <CardHeader><CardTitle>Attachments</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-2">
              {attachments.map(att => (
                <a key={att.id} href={att.file_path} target="_blank" rel="noreferrer"
                  className="flex items-center gap-2 text-sm text-blue-700 hover:underline p-2 border border-gray-100 rounded-lg">
                  <Paperclip size={13} />
                  <span className="flex-1 truncate">{att.file_name || 'Attachment'}</span>
                </a>
              ))}
            </CardContent>
          </Card>
        )}
      </div>
    </PortalLayout>
  )
}
