import { useEffect, useState } from 'react'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import Select from '@/components/ui/Select'
import Modal from '@/components/ui/Modal'
import Badge from '@/components/ui/Badge'
import { supabase } from '@/lib/supabase'
import { formatDateTime } from '@/lib/utils'
import { Plus, Trash2, Edit2 } from 'lucide-react'
import toast from 'react-hot-toast'

const ROLES = ['admin','respondent','lawyer','cast_member']
const roleVariant = { admin:'blue', respondent:'green', lawyer:'purple', cast_member:'orange' }

export default function UsersPage() {
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(true)
  const [showModal, setShowModal] = useState(false)
  const [editUser, setEditUser] = useState(null)
  const [form, setForm] = useState({ name: '', email: '', role: 'respondent', password: '' })
  const [saving, setSaving] = useState(false)

  const load = async () => {
    setLoading(true)
    const { data } = await supabase.from('profiles').select('*').order('created_at', { ascending: false })
    setUsers(data || [])
    setLoading(false)
  }

  useEffect(() => { load() }, [])

  const openCreate = () => { setEditUser(null); setForm({ name:'', email:'', role:'respondent', password:'' }); setShowModal(true) }
  const openEdit = (u) => { setEditUser(u); setForm({ name: u.name, email: u.email, role: u.role, password: '' }); setShowModal(true) }

  const handleSave = async () => {
    if (!form.name || !form.email) return toast.error('Name and email required')
    setSaving(true)
    try {
      if (editUser) {
        const { error } = await supabase.from('profiles').update({ name: form.name, role: form.role, updated_at: new Date().toISOString() }).eq('id', editUser.id)
        if (error) throw error
        toast.success('User updated')
      } else {
        if (!form.password) return toast.error('Password required for new user')
        // Create auth user via admin — requires service_role key (not available on client)
        // Instead, sign up with normal auth
        const { data, error } = await supabase.auth.signUp({
          email: form.email,
          password: form.password,
          options: { data: { name: form.name, role: form.role } }
        })
        if (error) throw error
        toast.success('User created — they may need to verify their email')
      }
      setShowModal(false)
      load()
    } catch (err) {
      toast.error(err.message || 'Failed to save user')
    } finally {
      setSaving(false)
    }
  }

  const handleDelete = async (userId) => {
    if (!confirm('Delete this user? This cannot be undone.')) return
    const { error } = await supabase.from('profiles').delete().eq('id', userId)
    if (error) toast.error('Failed to delete')
    else { toast.success('User deleted'); load() }
  }

  return (
    <AdminLayout>
      <div className="flex items-center justify-between mb-6">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Users</h1>
          <p className="text-gray-500 text-sm mt-1">{users.length} users</p>
        </div>
        <Button onClick={openCreate}><Plus size={16}/> Add User</Button>
      </div>

      <Card>
        <CardContent className="p-0">
          {loading ? (
            <div className="p-10 text-center text-gray-400">Loading...</div>
          ) : (
            <div className="overflow-x-auto">
              <table className="w-full text-sm">
                <thead>
                  <tr className="border-b border-gray-100 bg-gray-50 text-left">
                    <th className="px-4 py-3 font-medium text-gray-500">Name</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Email</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Role</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Joined</th>
                    <th className="px-4 py-3 font-medium text-gray-500">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {users.map(u => (
                    <tr key={u.id} className="border-b border-gray-50 hover:bg-gray-50">
                      <td className="px-4 py-3 font-medium text-gray-800">{u.name}</td>
                      <td className="px-4 py-3 text-gray-600">{u.email}</td>
                      <td className="px-4 py-3">
                        <Badge variant={roleVariant[u.role] || 'default'} className="capitalize">{u.role.replace('_',' ')}</Badge>
                      </td>
                      <td className="px-4 py-3 text-gray-400 text-xs">{formatDateTime(u.created_at)}</td>
                      <td className="px-4 py-3 flex items-center gap-2">
                        <Button variant="ghost" size="sm" onClick={() => openEdit(u)}><Edit2 size={13}/></Button>
                        <Button variant="ghost" size="sm" onClick={() => handleDelete(u.id)}><Trash2 size={13} className="text-red-500"/></Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}
        </CardContent>
      </Card>

      <Modal open={showModal} onClose={() => setShowModal(false)} title={editUser ? 'Edit User' : 'Add User'}>
        <div className="flex flex-col gap-4">
          <Input label="Name *" value={form.name} onChange={e => setForm(f=>({...f,name:e.target.value}))} />
          <Input label="Email *" type="email" value={form.email} onChange={e => setForm(f=>({...f,email:e.target.value}))} disabled={!!editUser} />
          {!editUser && <Input label="Password *" type="password" value={form.password} onChange={e => setForm(f=>({...f,password:e.target.value}))} />}
          <Select label="Role" value={form.role} onChange={e => setForm(f=>({...f,role:e.target.value}))}>
            {ROLES.map(r => <option key={r} value={r}>{r.replace('_',' ')}</option>)}
          </Select>
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => setShowModal(false)}>Cancel</Button>
            <Button onClick={handleSave} loading={saving}>Save</Button>
          </div>
        </div>
      </Modal>
    </AdminLayout>
  )
}
