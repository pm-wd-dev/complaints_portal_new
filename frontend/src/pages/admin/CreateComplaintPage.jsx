import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Textarea from '@/components/ui/Textarea'
import Select from '@/components/ui/Select'
import { supabase } from '@/lib/supabase'
import { generateCaseNumber } from '@/lib/utils'
import toast from 'react-hot-toast'
import { ArrowLeft } from 'lucide-react'

export default function CreateComplaintPage() {
  const navigate = useNavigate()
  const [loading, setLoading] = useState(false)
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
      const { data: { user } } = await supabase.auth.getUser()
      const caseNumber = generateCaseNumber('standard')
      const { data, error } = await supabase.from('complaints').insert({
        case_number: caseNumber, ...form,
        status: 'submitted',
        submitted_by_admin: true,
        submitted_by_admin_id: user?.id,
      }).select().single()
      if (error) throw error
      toast.success('Complaint created')
      navigate(`/admin/complaints/${data.id}`)
    } catch (err) {
      toast.error(err.message || 'Failed to create')
    } finally {
      setLoading(false)
    }
  }

  return (
    <AdminLayout>
      <div className="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="sm" onClick={() => navigate('/admin/complaints')}><ArrowLeft size={16}/> Back</Button>
        <h1 className="text-xl font-bold text-gray-900">Create Complaint</h1>
      </div>

      <form onSubmit={handleSubmit} className="max-w-2xl flex flex-col gap-5">
        <Card>
          <CardHeader><CardTitle>Complainant Information</CardTitle></CardHeader>
          <CardContent className="flex flex-col gap-4">
            <label className="flex items-center gap-2 text-sm text-gray-700">
              <input type="checkbox" checked={form.anonymity} onChange={e => set('anonymity', e.target.checked)} />
              Submit anonymously
            </label>
            {!form.anonymity && (
              <div className="grid grid-cols-2 gap-4">
                <Input label="Name" value={form.name} onChange={e => set('name', e.target.value)} />
                <Input label="Email" type="email" value={form.email} onChange={e => set('email', e.target.value)} />
                <Input label="Phone" value={form.phone_number} onChange={e => set('phone_number', e.target.value)} />
              </div>
            )}
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle>Complaint Details</CardTitle></CardHeader>
          <CardContent className="flex flex-col gap-4">
            <Select label="Type" value={form.complaint_type} onChange={e => set('complaint_type', e.target.value)}>
              <option value="">Select...</option>
              <option value="harassment">Harassment</option>
              <option value="discrimination">Discrimination</option>
              <option value="misconduct">Misconduct</option>
              <option value="policy_violation">Policy Violation</option>
              <option value="other">Other</option>
            </Select>
            <Textarea label="Description *" value={form.description} onChange={e => set('description', e.target.value)} rows={5} required />
            <Input label="Date of Experience" type="date" value={form.date_of_experience} onChange={e => set('date_of_experience', e.target.value)} />
          </CardContent>
        </Card>

        <Card>
          <CardHeader><CardTitle>Person Complained About</CardTitle></CardHeader>
          <CardContent className="grid grid-cols-2 gap-4">
            <Input label="Name" value={form.complainee_name} onChange={e => set('complainee_name', e.target.value)} />
            <Input label="Email" type="email" value={form.complainee_email} onChange={e => set('complainee_email', e.target.value)} />
            <div className="col-span-2">
              <Input label="Address" value={form.complainee_address} onChange={e => set('complainee_address', e.target.value)} />
            </div>
          </CardContent>
        </Card>

        <div className="flex gap-3 justify-end">
          <Button variant="secondary" onClick={() => navigate('/admin/complaints')}>Cancel</Button>
          <Button type="submit" loading={loading}>Create Complaint</Button>
        </div>
      </form>
    </AdminLayout>
  )
}
