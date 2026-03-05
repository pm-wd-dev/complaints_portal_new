import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useAuthStore } from '@/store/authStore'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import { Card, CardContent } from '@/components/ui/Card'
import { getErrorMessage } from '@/lib/supabase'
import toast from 'react-hot-toast'
import { ShieldCheck } from 'lucide-react'

export default function LoginPage() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [loading, setLoading] = useState(false)
  const { signIn } = useAuthStore()
  const navigate = useNavigate()

  const handleLogin = async (e) => {
    e.preventDefault()
    setLoading(true)
    try {
      await signIn(email, password)
      // signIn now fetches profile internally — read it from store
      const currentProfile = useAuthStore.getState().profile
      const role = currentProfile?.role

      if (!currentProfile) {
        toast.error('Account profile not found. Please contact support.')
        return
      }

      toast.success('Welcome!')

      if (role === 'admin') {
        navigate('/admin')
      } else if (role === 'cast_member') {
        navigate('/cast-member/dashboard')
      } else if (role === 'respondent') {
        navigate('/respondent/dashboard')
      } else if (role === 'lawyer') {
        navigate('/lawyer/dashboard')
      } else {
        toast.error('Your account does not have portal access')
      }
    } catch (err) {
      toast.error(getErrorMessage(err))
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        <div className="text-center mb-8">
          <div className="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <ShieldCheck className="text-white" size={28} />
          </div>
          <h1 className="text-2xl font-bold text-white">Staff Login</h1>
          <p className="text-blue-200 text-sm mt-1">Complaint Management System</p>
        </div>

        <Card>
          <CardContent className="py-6">
            <form onSubmit={handleLogin} className="flex flex-col gap-4">
              <Input
                label="Email"
                type="email"
                value={email}
                onChange={e => setEmail(e.target.value)}
                placeholder="you@example.com"
                required
              />
              <Input
                label="Password"
                type="password"
                value={password}
                onChange={e => setPassword(e.target.value)}
                placeholder="••••••••"
                required
              />
              <Button type="submit" loading={loading} className="w-full mt-2">
                Sign In
              </Button>
            </form>
          </CardContent>
        </Card>

        <div className="text-center mt-6 text-sm text-blue-200">
          <Link to="/" className="hover:text-white">&larr; Back to Public Portal</Link>
        </div>
      </div>
    </div>
  )
}
