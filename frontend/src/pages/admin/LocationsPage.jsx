import { useEffect, useState } from 'react'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Modal from '@/components/ui/Modal'
import { supabase } from '@/lib/supabase'
import { Plus, Trash2, Edit2, MapPin } from 'lucide-react'
import toast from 'react-hot-toast'

export default function LocationsPage() {
  const [locations, setLocations] = useState([])
  const [loading, setLoading] = useState(true)
  const [showModal, setShowModal] = useState(false)
  const [editLoc, setEditLoc] = useState(null)
  const [form, setForm] = useState({ name: '', city: '', state: '', address: '' })
  const [saving, setSaving] = useState(false)

  const load = async () => {
    setLoading(true)
    const { data } = await supabase.from('locations').select('*').order('created_at', { ascending: false })
    setLocations(data || [])
    setLoading(false)
  }

  useEffect(() => { load() }, [])

  const openCreate = () => { setEditLoc(null); setForm({ name:'', city:'', state:'', address:'' }); setShowModal(true) }
  const openEdit = (l) => { setEditLoc(l); setForm({ name:l.name, city:l.city||'', state:l.state||'', address:l.address||'' }); setShowModal(true) }

  const handleSave = async () => {
    if (!form.name.trim()) return toast.error('Name is required')
    setSaving(true)
    try {
      if (editLoc) {
        await supabase.from('locations').update({ ...form, updated_at: new Date().toISOString() }).eq('id', editLoc.id)
        toast.success('Location updated')
      } else {
        await supabase.from('locations').insert(form)
        toast.success('Location created')
      }
      setShowModal(false)
      load()
    } catch {
      toast.error('Failed to save')
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (id) => {
    if (!confirm('Delete this location?')) return
    await supabase.from('locations').delete().eq('id', id)
    toast.success('Deleted')
    load()
  }

  return (
    <AdminLayout>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Locations</h1>
          <p className="text-gray-500 text-sm mt-1">{locations.length} locations</p>
        </div>
        <Button onClick={openCreate}><Plus size={16}/> Add Location</Button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {loading ? (
          <div className="col-span-3 p-10 text-center text-gray-400">Loading...</div>
        ) : locations.length === 0 ? (
          <div className="col-span-3 p-10 text-center text-gray-400">No locations yet</div>
        ) : locations.map(l => (
          <Card key={l.id}>
            <CardContent className="py-4">
              <div className="flex items-start justify-between gap-2 mb-2">
                <div className="flex items-center gap-2">
                  <MapPin size={16} className="text-blue-600" />
                  <h3 className="font-semibold text-gray-900">{l.name}</h3>
                </div>
                <div className="flex gap-1">
                  <Button variant="ghost" size="sm" onClick={() => openEdit(l)}><Edit2 size={13}/></Button>
                  <Button variant="ghost" size="sm" onClick={() => handleDelete(l.id)}><Trash2 size={13} className="text-red-500"/></Button>
                </div>
              </div>
              {(l.city || l.state) && <p className="text-sm text-gray-500">{[l.city, l.state].filter(Boolean).join(', ')}</p>}
              {l.address && <p className="text-xs text-gray-400 mt-1">{l.address}</p>}
            </CardContent>
          </Card>
        ))}
      </div>

      <Modal open={showModal} onClose={() => setShowModal(false)} title={editLoc ? 'Edit Location' : 'Add Location'}>
        <div className="flex flex-col gap-4">
          <Input label="Name *" value={form.name} onChange={e => setForm(f=>({...f,name:e.target.value}))} placeholder="Main Office" />
          <div className="grid grid-cols-2 gap-4">
            <Input label="City" value={form.city} onChange={e => setForm(f=>({...f,city:e.target.value}))} />
            <Input label="State" value={form.state} onChange={e => setForm(f=>({...f,state:e.target.value}))} />
          </div>
          <Input label="Address" value={form.address} onChange={e => setForm(f=>({...f,address:e.target.value}))} />
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => setShowModal(false)}>Cancel</Button>
            <Button onClick={handleSave} loading={saving}>Save</Button>
          </div>
        </div>
      </Modal>
    </AdminLayout>
  )
}
