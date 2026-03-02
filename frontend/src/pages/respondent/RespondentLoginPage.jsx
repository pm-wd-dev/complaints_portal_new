import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Card, CardContent } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Input from '@/components/ui/Input'
import { supabase } from '@/lib/supabase'
import { useAuthStore } from '@/store/authStore'
import toast from 'react-hot-toast'
import { UserCheck } from 'lucide-react'

export default function RespondentLoginPage() {
  const [step, setStep] = useState('credentials') // 'credentials' | 'otp'
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [otp, setOtp] = useState('')
  const [loading, setLoading] = useState(false)
  const { signIn, setRespondentSession } = useAuthStore()
  const navigate = useNavigate()

  const handleLogin = async (e) => {
    e.preventDefault()
    setLoading(true)
    try {
      const { user } = await signIn(email, password)
      const { data: profile } = await supabase.from('profiles').select('role').eq('id', user.id).single()
      if (!['respondent','lawyer'].includes(profile?.role)) {
        await supabase.auth.signOut()
        return toast.error('Not authorized as respondent')
      }
      // In production send OTP email; here we simulate with step change
      toast.success('OTP sent to your email (use: 0000 for testing)')
      setStep('otp')
    } catch (err) {
      toast.error(err.message || 'Invalid credentials')
    } finally {
      setLoading(false)
    }
  }

  const handleOtp = async (e) => {
    e.preventDefault()
    // For demo: accept '0000'
    if (otp !== '0000') return toast.error('Invalid OTP')
    const { data: { user } } = await supabase.auth.getUser()
    const { data: profile } = await supabase.from('profiles').select('*').eq('id', user.id).single()
    setRespondentSession({ userId: user.id, profile })
    toast.success('Welcome!')
    navigate('/respondent/dashboard')
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-emerald-900 to-emerald-700 flex items-center justify-center p-4">
      <div className="w-full max-w-sm">
        <div className="text-center mb-8">
          <div className="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <UserCheck className="text-white" size={28} />
          </div>
          <h1 className="text-2xl font-bold text-white">Respondent Portal</h1>
          <p className="text-emerald-200 text-sm mt-1">Sign in to manage assigned complaints</p>
        </div>

        <Card>
          <CardContent className="py-6">
            {step === 'credentials' ? (
              <form onSubmit={handleLogin} className="flex flex-col gap-4">
                <Input label="Email" type="email" value={email} onChange={e => setEmail(e.target.value)} required />
                <Input label="Password" type="password" value={password} onChange={e => setPassword(e.target.value)} required />
                <Button type="submit" loading={loading} className="w-full mt-2">Continue</Button>
              </form>
            ) : (
              <form onSubmit={handleOtp} className="flex flex-col gap-4">
                <p className="text-sm text-gray-500 text-center">Enter the OTP sent to <strong>{email}</strong></p>
                <Input
                  label="OTP Code"
                  value={otp}
                  onChange={e => setOtp(e.target.value)}
                  placeholder="0000"
                  maxLength={6}
                  className="text-center text-xl tracking-widest"
                />
                <Button type="submit" className="w-full">Verify OTP</Button>
                <button type="button" onClick={() => setStep('credentials')} className="text-xs text-gray-400 hover:text-gray-600 text-center">← Back</button>
              </form>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
