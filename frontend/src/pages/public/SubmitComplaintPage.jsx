import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PublicLayout from '@/components/layout/PublicLayout'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Textarea from '@/components/ui/Textarea'
import Select from '@/components/ui/Select'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import { supabase } from '@/lib/supabase'
import { generateCaseNumber } from '@/lib/utils'
import toast from 'react-hot-toast'
import { CheckCircle } from 'lucide-react'

export default function SubmitComplaintPage() {
  const navigate = useNavigate()
  const [loading, setLoading] = useState(false)
  const [submitted, setSubmitted] = useState(null)
  const [form, setForm] = useState({
    name: '', email: '', phone_number: '', description: '',
    complaint_type: '', complaint_about: '',
    complainee_name: '', complainee_email: '', complainee_address: '',
    witnesses: '', evidence_type: 'none', evidence_description: '',
    date_of_experience: '', anonymity: false, submitted_as: 'individual',
  })

  const set = (key, val) => setForm(f => ({ ...f, [key]: val }))

  const handleSubmit = async (e) => {
    e.preventDefault()
    if (!form.description.trim()) return toast.error('Description is required')
    setLoading(true)
    try {
      const caseNumber = generateCaseNumber('guest')
      const { error } = await supabase.from('complaints').insert({
        case_number: caseNumber,
        ...form,
        anonymity: form.anonymity,
        status: 'submitted',
      })
      if (error) throw error
      setSubmitted(caseNumber)
    } catch (err) {
      toast.error(err.message || 'Failed to submit complaint')
    } finally {
      setLoading(false)
    }
  }

  if (submitted) {
    return (
      <PublicLayout>
        <div className="max-w-xl mx-auto py-16 px-4 text-center">
          <div className="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CheckCircle className="text-green-600" size={32} />
          </div>
          <h2 className="text-2xl font-bold text-gray-900 mb-2">Complaint Submitted!</h2>
          <p className="text-gray-500 mb-4">Your complaint has been received. Use the case number below to track its status.</p>
          <div className="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <p className="text-xs text-blue-600 mb-1">Your Case Number</p>
            <p className="text-2xl font-bold text-blue-900">{submitted}</p>
          </div>
          <p className="text-sm text-gray-400 mb-6">Please save this case number. You'll need it to track your complaint.</p>
          <div className="flex gap-3 justify-center">
            <Button variant="outline" onClick={() => navigate(`/track`)}>Track Complaint</Button>
            <Button onClick={() => { setSubmitted(null); setForm({ name:'',email:'',phone_number:'',description:'',complaint_type:'',complaint_about:'',complainee_name:'',complainee_email:'',complainee_address:'',witnesses:'',evidence_type:'none',evidence_description:'',date_of_experience:'',anonymity:false,submitted_as:'individual' }) }}>
              Submit Another
            </Button>
          </div>
        </div>
      </PublicLayout>
    )
  }

  return (
    <PublicLayout>
      <div className="max-w-2xl mx-auto py-8 px-4">
        <div className="mb-6">
          <h1 className="text-2xl font-bold text-gray-900">Submit a Complaint</h1>
          <p className="text-gray-500 text-sm mt-1">All fields marked with * are required</p>
        </div>

        <form onSubmit={handleSubmit} className="flex flex-col gap-5">
          {/* Complainant Info */}
          <Card>
            <CardHeader><CardTitle>Your Information</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-4">
              <Select label="Submitting as" value={form.submitted_as} onChange={e => set('submitted_as', e.target.value)}>
                <option value="individual">Individual</option>
                <option value="organization">Organization</option>
              </Select>
              <label className="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" checked={form.anonymity} onChange={e => set('anonymity', e.target.checked)} className="rounded" />
                Submit anonymously (your name won't be shared)
              </label>
              {!form.anonymity && (
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Input label="Full Name" value={form.name} onChange={e => set('name', e.target.value)} placeholder="John Doe" />
                  <Input label="Email" type="email" value={form.email} onChange={e => set('email', e.target.value)} placeholder="john@example.com" />
                  <Input label="Phone Number" value={form.phone_number} onChange={e => set('phone_number', e.target.value)} placeholder="+1 234 567 8900" />
                </div>
              )}
            </CardContent>
          </Card>

          {/* Complaint Details */}
          <Card>
            <CardHeader><CardTitle>Complaint Details</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-4">
              <Select label="Complaint Type" value={form.complaint_type} onChange={e => set('complaint_type', e.target.value)}>
                <option value="">Select type...</option>
                <option value="harassment">Harassment</option>
                <option value="discrimination">Discrimination</option>
                <option value="misconduct">Misconduct</option>
                <option value="policy_violation">Policy Violation</option>
                <option value="other">Other</option>
              </Select>
              <Select label="Complaint About" value={form.complaint_about} onChange={e => set('complaint_about', e.target.value)}>
                <option value="">Select...</option>
                <option value="employee">Employee</option>
                <option value="management">Management</option>
                <option value="service">Service</option>
                <option value="product">Product</option>
                <option value="other">Other</option>
              </Select>
              <Textarea
                label="Description *"
                value={form.description}
                onChange={e => set('description', e.target.value)}
                placeholder="Describe your complaint in detail..."
                rows={5}
                required
              />
              <Input
                label="Date of Experience"
                type="date"
                value={form.date_of_experience}
                onChange={e => set('date_of_experience', e.target.value)}
              />
            </CardContent>
          </Card>

          {/* Person Complained About */}
          <Card>
            <CardHeader><CardTitle>Person Complained About</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <Input label="Name" value={form.complainee_name} onChange={e => set('complainee_name', e.target.value)} />
                <Input label="Email" type="email" value={form.complainee_email} onChange={e => set('complainee_email', e.target.value)} />
              </div>
              <Input label="Address" value={form.complainee_address} onChange={e => set('complainee_address', e.target.value)} />
            </CardContent>
          </Card>

          {/* Evidence */}
          <Card>
            <CardHeader><CardTitle>Evidence & Witnesses</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-4">
              <Select label="Evidence Type" value={form.evidence_type} onChange={e => set('evidence_type', e.target.value)}>
                <option value="none">No Evidence</option>
                <option value="photos">Photos</option>
                <option value="videos">Videos</option>
                <option value="messages">Messages / Emails</option>
                <option value="documents">Documents</option>
              </Select>
              {form.evidence_type !== 'none' && (
                <Textarea
                  label="Describe your evidence"
                  value={form.evidence_description}
                  onChange={e => set('evidence_description', e.target.value)}
                  rows={3}
                />
              )}
              <Textarea
                label="Witnesses"
                value={form.witnesses}
                onChange={e => set('witnesses', e.target.value)}
                placeholder="List any witnesses (name and contact if available)"
                rows={3}
              />
            </CardContent>
          </Card>

          <Button type="submit" size="lg" loading={loading} className="w-full">
            Submit Complaint
          </Button>
        </form>
      </div>
    </PublicLayout>
  )
}
