import { useEffect, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PortalLayout from '@/components/layout/PortalLayout'
import { Card, CardContent } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import Textarea from '@/components/ui/Textarea'
import Modal from '@/components/ui/Modal'
import { supabase } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import { formatDateTime, getStatusLabel } from '@/lib/utils'
import { MessageSquare } from 'lucide-react'
import toast from 'react-hot-toast'

const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

export default function LawyerDashboard() {
  const { user, profile, signOut } = useAuthStore()
  const navigate = useNavigate()
  const [assignments, setAssignments] = useState([])
  const [loading, setLoading] = useState(true)
  const [showResponseModal, setShowResponseModal] = useState(null)
  const [responseText, setResponseText] = useState('')
  const [saving, setSaving] = useState(false)

  useEffect(() => {
    if (user) loadAssignments()
  }, [user])

  const loadAssignments = async () => {
    setLoading(true)
    const { data } = await supabase
      .from('complaint_lawyers')
      .select('*, complaints(*)')
      .eq('user_id', user.id)
      .order('created_at', { ascending: false })
    setAssignments(data || [])
    setLoading(false)
  }

  const handleSignOut = async () => {
    await signOut()
    navigate('/login')
  }

  const submitInput = async () => {
    if (!responseText.trim()) return toast.error('Input cannot be empty')
    setSaving(true)
    try {
      await supabase.from('complaint_lawyers').update({
        input: responseText,
        responded_at: new Date().toISOString()
      }).eq('id', showResponseModal.id)
      toast.success('Legal input submitted')
      setShowResponseModal(null)
      setResponseText('')
      loadAssignments()
    } catch (err) {
      toast.error('Failed to submit')
    } finally {
      setSaving(false)
    }
  }

  return (
    <PortalLayout title="Lawyer Portal" role="lawyer" onSignOut={handleSignOut} userName={profile?.name}>
      <div className="mb-6">
        <h2 className="text-xl font-bold text-gray-900">Assigned Cases</h2>
        <p className="text-gray-500 text-sm mt-1">{assignments.length} cases assigned</p>
      </div>

      {loading ? (
        <div className="text-center text-gray-400 py-12">Loading...</div>
      ) : assignments.length === 0 ? (
        <div className="text-center text-gray-400 py-12">No cases assigned to you</div>
      ) : (
        <div className="flex flex-col gap-4">
          {assignments.map(a => (
            <Card key={a.id}>
              <CardContent className="py-4">
                <div className="flex items-start justify-between gap-4">
                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="font-mono text-xs text-purple-700 font-medium">{a.complaints.case_number}</span>
                      <Badge variant={statusVariant[a.complaints.status] || 'default'}>
                        {getStatusLabel(a.complaints.status)}
                      </Badge>
                    </div>
                    <p className="text-sm text-gray-700 line-clamp-2">{a.complaints.description}</p>
                    <p className="text-xs text-gray-400 mt-1">Assigned {formatDateTime(a.created_at)}</p>
                    {a.responded_at && (
                      <p className="text-xs text-green-600 mt-1">✓ Input submitted {formatDateTime(a.responded_at)}</p>
                    )}
                    {a.input && (
                      <div className="mt-3 bg-purple-50 rounded-lg p-3">
                        <p className="text-xs text-gray-400 mb-1">Your legal input:</p>
                        <p className="text-sm text-gray-700">{a.input}</p>
                      </div>
                    )}
                  </div>
                  <div className="flex flex-col gap-2">
                    <Button size="sm" variant="outline" onClick={() => navigate(`/lawyer/complaints/${a.complaint_id}`)}>
                      <MessageSquare size={13}/> View Case
                    </Button>
                    {!a.responded_at && (
                      <Button size="sm" onClick={() => { setShowResponseModal(a); setResponseText('') }}>
                        Add Input
                      </Button>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      )}

      <Modal open={!!showResponseModal} onClose={() => setShowResponseModal(null)} title="Submit Legal Input">
        <div className="flex flex-col gap-4">
          {showResponseModal && (
            <div className="bg-gray-50 rounded-lg p-3 text-sm">
              <p className="text-xs text-gray-400 mb-1">Case {showResponseModal.complaints.case_number}</p>
              <p className="text-gray-700">{showResponseModal.complaints.description}</p>
            </div>
          )}
          <Textarea label="Legal Input / Opinion *" value={responseText} onChange={e => setResponseText(e.target.value)} rows={6} placeholder="Write your legal assessment..." />
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => setShowResponseModal(null)}>Cancel</Button>
            <Button onClick={submitInput} loading={saving}>Submit</Button>
          </div>
        </div>
      </Modal>
    </PortalLayout>
  )
}
