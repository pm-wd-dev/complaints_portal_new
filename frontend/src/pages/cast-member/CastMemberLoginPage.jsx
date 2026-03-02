import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { Card, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import { supabase } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import toast from 'react-hot-toast'
import { Users } from 'lucide-react'

export default function CastMemberLoginPage() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const { signIn } = useAuthStore()
  const navigate = useNavigate()

  const handleLogin = async (e) => {
    e.preventDefault()
    setLoading(true)
    try {
      const { user } = await signIn(email, password)
      const { data: profile } = await supabase.from('profiles').select('role').eq('id', user.id).single()
      if (profile?.role !== 'cast_member') {
        await supabase.auth.signOut()
        return toast.error('Not authorized as cast member')
      }
      toast.success('Welcome!')
      navigate('/cast-member/dashboard')
    } catch (err) {
      toast.error(err.message || 'Invalid credentials')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-orange-800 to-orange-600 flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        <div className="text-center mb-8">
          <div className="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <Users className="text-white" size={28} />
          </div>
          <h1 className="text-2xl font-bold text-white">Cast Member Portal</h1>
          <p className="text-orange-200 text-sm mt-1">Submit and manage complaints</p>
        </div>
        <Card>
          <CardContent className="py-6">
            <form onSubmit={handleLogin} className="flex flex-col gap-4">
              <Input label="Email" type="email" value={email} onChange={e => setEmail(e.target.value)} required />
              <Input label="Password" type="password" value={password} onChange={e => setPassword(e.target.value)} required />
              <Button type="submit" loading={loading} className="w-full mt-2">Sign In</Button>
            </form>
          </CardContent>
        </Card>
        <div className="text-center mt-4">
          <Link to="/login" className="text-orange-200 text-sm hover:text-white">← Staff Login</Link>
        </div>
      </div>
    </div>
  )
}
