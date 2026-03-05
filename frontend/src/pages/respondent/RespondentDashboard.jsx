import { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'
import PortalLayout from '@/components/layout/PortalLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Badge from '@/components/ui/Badge'
import Button from '@/components/ui/Button'
import Textarea from '@/components/ui/Textarea'
import Modal from '@/components/ui/Modal'
import { supabase, getErrorMessage } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import { formatDateTime, getStatusLabel } from '@/lib/utils'
import { Eye, MessageSquare, ExternalLink } from 'lucide-react'
import toast from 'react-hot-toast'

const statusVariant = { submitted:'blue', under_review:'yellow', escalated:'orange', resolved:'green', closed:'default' }

export default function RespondentDashboard() {
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
      .from('complaint_respondents')
      .select('*, complaints(*), complaint_responses(*)')
      .eq('user_id', user.id)
      .order('created_at', { ascending: false })
    setAssignments(data || [])
    setLoading(false)
  }

  const handleSignOut = async () => {
    await signOut()
    navigate('/login')
  }

  const submitResponse = async () => {
    if (!responseText.trim()) return toast.error('Response cannot be empty')
    setSaving(true)
    try {
      const { error } = await supabase.from('complaint_responses').insert({
        complaint_respondent_id: showResponseModal.id,
        response: responseText,
      })
      if (error) throw error
      await supabase.from('complaint_respondents').update({ responded_at: new Date().toISOString(), input: responseText }).eq('id', showResponseModal.id)
      toast.success('Response submitted')
      setShowResponseModal(null)
      setResponseText('')
      loadAssignments()
    } catch (err) {
      toast.error(getErrorMessage(err))
    } finally {
      setSaving(false)
    }
  }

  return (
    <PortalLayout title="Respondent Dashboard" role="respondent" onSignOut={handleSignOut} userName={profile?.name}>
      <div className="mb-6">
        <h2 className="text-xl font-bold text-gray-900">My Assigned Complaints</h2>
        <p className="text-gray-500 text-sm mt-1">{assignments.length} complaints assigned to you</p>
      </div>

      {loading ? (
        <div className="text-center text-gray-400 py-12">Loading...</div>
      ) : assignments.length === 0 ? (
        <div className="text-center text-gray-400 py-12">No complaints assigned to you yet</div>
      ) : (
        <div className="flex flex-col gap-4">
          {assignments.map(a => (
            <Card key={a.id}>
              <CardContent className="py-4">
                <div className="flex items-start justify-between gap-4">
                  <div className="flex-1">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="font-mono text-xs text-blue-700 font-medium">{a.complaints.case_number}</span>
                      <Badge variant={statusVariant[a.complaints.status] || 'default'}>
                        {getStatusLabel(a.complaints.status)}
                      </Badge>
                    </div>
                    <p className="text-sm text-gray-700 line-clamp-2">{a.complaints.description}</p>
                    <p className="text-xs text-gray-400 mt-1">Assigned {formatDateTime(a.created_at)}</p>
                    {a.responded_at && (
                      <p className="text-xs text-green-600 mt-1">✓ Responded {formatDateTime(a.responded_at)}</p>
                    )}
                    {a.complaint_responses?.length > 0 && (
                      <div className="mt-3 bg-gray-50 rounded-lg p-3">
                        <p className="text-xs text-gray-400 mb-1">Your response:</p>
                        <p className="text-sm text-gray-700">{a.complaint_responses[0].response}</p>
                      </div>
                    )}
                  </div>
                  <div className="flex flex-col gap-2">
                    <Button size="sm" variant="outline" onClick={() => navigate(`/respondent/complaints/${a.complaint_id}`)}>
                      <Eye size={13}/> View
                    </Button>
                    {!a.responded_at && (
                      <Button size="sm" onClick={() => { setShowResponseModal(a); setResponseText('') }}>
                        <MessageSquare size={13}/> Respond
                      </Button>
                    )}
                  </div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      )}

      <Modal open={!!showResponseModal} onClose={() => setShowResponseModal(null)} title="Submit Response">
        <div className="flex flex-col gap-4">
          {showResponseModal && (
            <div className="bg-gray-50 rounded-lg p-3 text-sm">
              <p className="text-xs text-gray-400 mb-1">Complaint</p>
              <p className="text-gray-700">{showResponseModal.complaints.description}</p>
            </div>
          )}
          <Textarea label="Your Response *" value={responseText} onChange={e => setResponseText(e.target.value)} rows={5} placeholder="Write your response..." />
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => setShowResponseModal(null)}>Cancel</Button>
            <Button onClick={submitResponse} loading={saving}>Submit Response</Button>
          </div>
        </div>
      </Modal>
    </PortalLayout>
  )
}
