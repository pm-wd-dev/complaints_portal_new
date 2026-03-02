import { useState } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useAuthStore } from '@/store/authStore'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import { Card, CardContent } from '@/components/ui/Card'
import { supabase, getErrorMessage } from '@/lib/supabase'
import toast from 'react-hot-toast'
import { ShieldCheck } from 'lucide-react'

export default function LoginPage() {
  const [step, setStep] = useState('credentials') // 'credentials' | 'otp'
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [otp, setOtp] = useState('')
  const [loading, setLoading] = useState(false)
  const [pendingRole, setPendingRole] = useState(null)
  const [pendingUser, setPendingUser] = useState(null)
  const [pendingProfile, setPendingProfile] = useState(null)
  const { signIn, setRespondentSession, setLawyerSession } = useAuthStore()
  const navigate = useNavigate()

  const handleLogin = async (e) => {
    e.preventDefault()
    setLoading(true)
    try {
      const { user } = await signIn(email, password)
      const { data: profile } = await supabase
        .from('profiles')
        .select('*')
        .eq('id', user.id)
        .single()

      const role = profile?.role

      if (role === 'admin') {
        navigate('/admin')
      } else if (role === 'cast_member') {
        navigate('/cast-member/dashboard')
      } else if (role === 'respondent' || role === 'lawyer') {
        // Store pending auth, move to OTP step
        setPendingRole(role)
        setPendingUser(user)
        setPendingProfile(profile)
        toast.success('OTP sent to your email (use: 0000 for testing)')
        setStep('otp')
      } else {
        await supabase.auth.signOut()
        toast.error('Your account does not have portal access')
      }
    } catch (err) {
      toast.error(getErrorMessage(err))
    } finally {
      setLoading(false)
    }
  }

  const handleOtp = async (e) => {
    e.preventDefault()
    if (otp !== '0000') return toast.error('Invalid OTP')
    if (pendingRole === 'respondent') {
      setRespondentSession({ userId: pendingUser.id, profile: pendingProfile })
      toast.success('Welcome!')
      navigate('/respondent/dashboard')
    } else {
      setLawyerSession({ userId: pendingUser.id, profile: pendingProfile })
      toast.success('Welcome!')
      navigate('/lawyer/dashboard')
    }
  }

  const roleLabel = pendingRole === 'respondent' ? 'Respondent' : 'Lawyer'

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        <div className="text-center mb-8">
          <div className="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <ShieldCheck className="text-white" size={28} />
          </div>
          <h1 className="text-2xl font-bold text-white">
            {step === 'otp' ? `${roleLabel} Verification` : 'Staff Login'}
          </h1>
          <p className="text-blue-200 text-sm mt-1">Complaint Management System</p>
        </div>

        <Card>
          <CardContent className="py-6">
            {step === 'credentials' ? (
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
            ) : (
              <form onSubmit={handleOtp} className="flex flex-col gap-4">
                <p className="text-sm text-gray-500 text-center">
                  Enter the OTP sent to <strong>{email}</strong>
                </p>
                <Input
                  label="OTP Code"
                  value={otp}
                  onChange={e => setOtp(e.target.value)}
                  placeholder="0000"
                  maxLength={6}
                  className="text-center text-xl tracking-widest"
                  required
                />
                <Button type="submit" className="w-full">Verify OTP</Button>
                <button
                  type="button"
                  onClick={() => { setStep('credentials'); setOtp('') }}
                  className="text-xs text-gray-400 hover:text-gray-600 text-center"
                >
                  ← Back to login
                </button>
              </form>
            )}
          </CardContent>
        </Card>

        <div className="text-center mt-6 text-sm text-blue-200">
          <Link to="/" className="hover:text-white">← Back to Public Portal</Link>
        </div>
      </div>
    </div>
  )
}
