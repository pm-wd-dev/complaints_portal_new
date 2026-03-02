import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PortalLayout from '@/components/layout/PortalLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Textarea from '@/components/ui/Textarea'
import { supabase, getErrorMessage } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import { generateCaseNumber } from '@/lib/utils'
import toast from 'react-hot-toast'
import { ArrowLeft, Upload, Info } from 'lucide-react'

const EVIDENCE_OPTIONS = [
  { value: 'photos', label: '📸 Photos/Screenshots' },
  { value: 'videos', label: '🎥 Videos' },
  { value: 'messages', label: '📧 Messages/Emails' },
  { value: 'documents', label: '📝 Other Documents' },
  { value: 'none', label: '❌ No supporting evidence' },
]

export default function CastMemberCreateComplaint() {
  const { user, profile, signOut } = useAuthStore()
  const navigate = useNavigate()
  const [loading, setLoading] = useState(false)
  const [evidenceFile, setEvidenceFile] = useState(null)
  const [form, setForm] = useState({
    name: profile?.name || '',
    date_of_experience: '',
    complaint_about: '',
    complainee_name: '',
    complainee_email: '',
    description: '',
    witnesses: '',
    evidenceTypes: [],
    evidence_description: '',
  })

  const set = (k, v) => setForm(f => ({ ...f, [k]: v }))

  const toggleEvidence = (value) => {
    setForm(f => {
      if (value === 'none') {
        // Selecting "No evidence" clears everything else
        return { ...f, evidenceTypes: f.evidenceTypes.includes('none') ? [] : ['none'] }
      }
      // Selecting any evidence type removes "none"
      const without = f.evidenceTypes.filter(v => v !== 'none')
      const updated = without.includes(value)
        ? without.filter(v => v !== value)
        : [...without, value]
      return { ...f, evidenceTypes: updated }
    })
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!form.name.trim()) return toast.error('Your name is required')
    if (!form.date_of_experience) return toast.error('Date of complaint is required')
    if (!form.complaint_about.trim()) return toast.error('Please specify who or what the complaint is about')
    if (!form.description.trim()) return toast.error('Issue description is required')
    if (!form.witnesses.trim()) return toast.error('Please fill in the witnesses field')
    if (form.evidenceTypes.length === 0) return toast.error('Please select at least one evidence option')

    setLoading(true)
    try {
      const caseNumber = generateCaseNumber('cast')
      const evidenceTypeStr = form.evidenceTypes.join(',')

      const { data, error } = await supabase.from('complaints').insert({
        case_number: caseNumber,
        name: form.name,
        email: profile?.email || user?.email || '',
        date_of_experience: form.date_of_experience,
        complaint_about: form.complaint_about,
        complainee_name: form.complainee_name,
        complainee_email: form.complainee_email,
        description: form.description,
        witnesses: form.witnesses,
        evidence_type: evidenceTypeStr,
        evidence_description: form.evidence_description,
        submitted_as: 'individual',
        status: 'submitted',
        user_id: user?.id,
      }).select().single()

      if (error) throw error

      // Upload evidence file if provided
      if (evidenceFile && data) {
        const ext = evidenceFile.name.split('.').pop()
        const path = `complaints/${data.id}/evidence-${Date.now()}.${ext}`
        const { error: uploadErr } = await supabase.storage
          .from('complaint-attachments')
          .upload(path, evidenceFile)
        if (!uploadErr) {
          const { data: { publicUrl } } = supabase.storage
            .from('complaint-attachments')
            .getPublicUrl(path)
          await supabase.from('attachments').insert({
            complaint_id: data.id,
            file_path: publicUrl,
            file_name: evidenceFile.name,
            file_type: evidenceFile.type,
            file_size: evidenceFile.size,
            uploaded_by: user?.id,
            type: 'evidence',
          })
        }
      }

      toast.success('Complaint submitted successfully!')
      navigate(`/cast-member/complaints/${data.id}`)
    } catch (err) {
      toast.error(getErrorMessage(err))
    } finally {
      setLoading(false)
    }
  }

  return (
    <PortalLayout
      title="Cast Member Portal"
      role="cast_member"
      onSignOut={async () => { await signOut(); navigate('/cast-member/login') }}
      userName={profile?.name}
    >
      <div className="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="sm" onClick={() => navigate('/cast-member/dashboard')}>
          <ArrowLeft size={15} /> Back
        </Button>
        <div>
          <h2 className="text-xl font-bold text-gray-900">Cast Complaint Documentation</h2>
          <p className="text-sm text-gray-500 mt-0.5">
            Submission Form — Please provide as much detail as possible.
          </p>
        </div>
      </div>

      <form onSubmit={handleSubmit} className="max-w-2xl flex flex-col gap-5">

        {/* Email (auto-filled) */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Email <span className="text-red-500">*</span>
            </label>
            <div className="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-600">
              {profile?.email || user?.email || '—'}
            </div>
            <p className="text-xs text-gray-400 mt-1">Auto-filled from your account</p>
          </CardContent>
        </Card>

        {/* Case Number (auto-generated) */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <label className="block text-sm font-medium text-gray-700 mb-1">Case Number</label>
            <div className="flex items-center gap-2 px-3 py-2 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
              <Info size={14} className="flex-shrink-0" />
              Leave blank — will be auto-generated upon submission
            </div>
          </CardContent>
        </Card>

        {/* Your Name */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <Input
              label="Your Name — Who is making this complaint? *"
              value={form.name}
              onChange={e => set('name', e.target.value)}
              placeholder="Full name"
              required
            />
          </CardContent>
        </Card>

        {/* Date of Complaint */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <Input
              label="Date of Complaint — When did this issue occur? *"
              type="date"
              value={form.date_of_experience}
              onChange={e => set('date_of_experience', e.target.value)}
              required
            />
          </CardContent>
        </Card>

        {/* Who is the complaint about */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <Textarea
              label="Who or what is your complaint about? A specific person, event, or situation? *"
              value={form.complaint_about}
              onChange={e => set('complaint_about', e.target.value)}
              placeholder="Describe who or what this complaint is about..."
              rows={2}
              required
            />
          </CardContent>
        </Card>

        {/* Complainee info */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <p className="text-sm font-medium text-gray-700 mb-3">
              If you answered a person, please give (if you have it) their full name and email address. <span className="text-red-500">*</span>
            </p>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <Input
                label="Full Name"
                value={form.complainee_name}
                onChange={e => set('complainee_name', e.target.value)}
                placeholder="Person's full name"
              />
              <Input
                label="Email Address"
                type="email"
                value={form.complainee_email}
                onChange={e => set('complainee_email', e.target.value)}
                placeholder="person@example.com"
              />
            </div>
          </CardContent>
        </Card>

        {/* Description */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <Textarea
              label="Describe the issue in detail — What happened? Where did it take place? Who was involved? Be as detailed as possible. *"
              value={form.description}
              onChange={e => set('description', e.target.value)}
              placeholder="Provide a full account of what happened..."
              rows={6}
              required
            />
          </CardContent>
        </Card>

        {/* Witnesses */}
        <Card>
          <CardContent className="pt-5 pb-4">
            <Textarea
              label="Were there any witnesses? — If yes, please list their names and contact information. *"
              value={form.witnesses}
              onChange={e => set('witnesses', e.target.value)}
              placeholder="e.g. Jane Smith – jane@example.com, or 'No witnesses'"
              rows={3}
              required
            />
          </CardContent>
        </Card>

        {/* Evidence type (multi-select checkboxes) */}
        <Card>
          <CardHeader>
            <CardTitle>Do you have any supporting evidence? <span className="text-red-500">*</span></CardTitle>
          </CardHeader>
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
                value={form.evidence_description}
                onChange={e => set('evidence_description', e.target.value)}
                placeholder="Describe your evidence (what it shows, where it was taken, etc.)"
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
                {evidenceFile && (
                  <p className="text-xs text-green-600 mt-2">Selected: {evidenceFile.name}</p>
                )}
              </div>
            </CardContent>
          </Card>
        )}

        <div className="flex gap-3 justify-end pb-6">
          <Button variant="secondary" type="button" onClick={() => navigate('/cast-member/dashboard')}>
            Cancel
          </Button>
          <Button type="submit" loading={loading} size="lg">
            Submit Complaint
          </Button>
        </div>
      </form>
    </PortalLayout>
  )
}
