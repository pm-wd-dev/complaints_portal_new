import { useEffect, useState } from 'react'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Modal from '@/components/ui/Modal'
import { supabase } from '@/lib/supabase'
import { Plus, Trash2, Edit2, GripVertical, ToggleLeft, ToggleRight } from 'lucide-react'
import toast from 'react-hot-toast'

export default function StagesPage() {
  const [stages, setStages] = useState([])
  const [loading, setLoading] = useState(true)
  const [showModal, setShowModal] = useState(false)
  const [editStage, setEditStage] = useState(null)
  const [form, setForm] = useState({ name: '', step_number: 1, color: '#3b82f6' })
  const [saving, setSaving] = useState(false)

  const load = async () => {
    setLoading(true)
    const { data } = await supabase.from('stages').select('*').order('step_number')
    setStages(data || [])
    setLoading(false)
  }

  useEffect(() => { load() }, [])

  const openCreate = () => {
    const nextStep = stages.length + 1
    setEditStage(null)
    setForm({ name: '', step_number: nextStep, color: '#3b82f6' })
    setShowModal(true)
  }

  const openEdit = (s) => {
    setEditStage(s)
    setForm({ name: s.name, step_number: s.step_number, color: s.color })
    setShowModal(true)
  }

  const handleSave = async () => {
    if (!form.name.trim()) return toast.error('Name is required')
    setSaving(true)
    try {
      if (editStage) {
        await supabase.from('stages').update({ ...form, updated_at: new Date().toISOString() }).eq('id', editStage.id)
        toast.success('Stage updated')
      } else {
        await supabase.from('stages').insert(form)
        toast.success('Stage created')
      }
      setShowModal(false)
      load()
    } catch (err) {
      toast.error('Failed to save')
    } finally {
      setSaving(false)
    }
  }

  const toggleActive = async (stage) => {
    await supabase.from('stages').update({ is_active: !stage.is_active }).eq('id', stage.id)
    toast.success(`Stage ${stage.is_active ? 'deactivated' : 'activated'}`)
    load()
  }

  const handleDelete = async (id) => {
    if (!confirm('Delete this stage?')) return
    await supabase.from('stages').delete().eq('id', id)
    toast.success('Deleted')
    load()
  }

  return (
    <AdminLayout>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Complaint Stages</h1>
          <p className="text-gray-500 text-sm mt-1">Manage the workflow stages for complaints</p>
        </div>
        <Button onClick={openCreate}><Plus size={16}/> Add Stage</Button>
      </div>

      <Card>
        <CardContent className="p-0">
          {loading ? (
            <div className="p-10 text-center text-gray-400">Loading...</div>
          ) : stages.length === 0 ? (
            <div className="p-10 text-center text-gray-400">No stages yet</div>
          ) : (
            <div>
              {stages.map(s => (
                <div key={s.id} className="flex items-center gap-4 px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                  <div className="w-5 h-5 rounded-full flex-shrink-0" style={{ backgroundColor: s.color }} />
                  <div className="flex-1">
                    <div className="flex items-center gap-2">
                      <span className="font-medium text-gray-800">{s.name}</span>
                      <span className="text-xs text-gray-400">Step {s.step_number}</span>
                      {!s.is_active && <span className="text-xs text-red-400 bg-red-50 px-1.5 py-0.5 rounded">Inactive</span>}
                    </div>
                  </div>
                  <div className="flex items-center gap-2">
                    <button onClick={() => toggleActive(s)} className="text-gray-400 hover:text-blue-600 transition-colors">
                      {s.is_active ? <ToggleRight size={18} className="text-blue-600"/> : <ToggleLeft size={18}/>}
                    </button>
                    <Button variant="ghost" size="sm" onClick={() => openEdit(s)}><Edit2 size={13}/></Button>
                    <Button variant="ghost" size="sm" onClick={() => handleDelete(s.id)}><Trash2 size={13} className="text-red-500"/></Button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </CardContent>
      </Card>

      <Modal open={showModal} onClose={() => setShowModal(false)} title={editStage ? 'Edit Stage' : 'Add Stage'}>
        <div className="flex flex-col gap-4">
          <Input label="Stage Name *" value={form.name} onChange={e => setForm(f=>({...f,name:e.target.value}))} placeholder="e.g. Initial Review" />
          <Input label="Step Number" type="number" min={1} value={form.step_number} onChange={e => setForm(f=>({...f,step_number:parseInt(e.target.value)}))} />
          <div className="flex flex-col gap-1">
            <label className="text-sm font-medium text-gray-700">Color</label>
            <div className="flex items-center gap-3">
              <input type="color" value={form.color} onChange={e => setForm(f=>({...f,color:e.target.value}))} className="w-10 h-10 rounded cursor-pointer border border-gray-200" />
              <span className="text-sm text-gray-500">{form.color}</span>
            </div>
          </div>
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => setShowModal(false)}>Cancel</Button>
            <Button onClick={handleSave} loading={saving}>Save</Button>
          </div>
        </div>
      </Modal>
    </AdminLayout>
  )
}
