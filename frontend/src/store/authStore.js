import { create } from 'zustand'
import { supabase } from '@/lib/supabase'

export const useAuthStore = create((set, get) => ({
  user: null,
  profile: null,
  loading: true,

  setUser: (user) => set({ user }),
  setProfile: (profile) => set({ profile }),
  setLoading: (loading) => set({ loading }),

  initialize: async () => {
    set({ loading: true })
    const { data: { session } } = await supabase.auth.getSession()
    if (session?.user) {
      set({ user: session.user })
      await get().fetchProfile(session.user.id)
    }
    set({ loading: false })

    supabase.auth.onAuthStateChange(async (_event, session) => {
      if (session?.user) {
        set({ user: session.user })
        await get().fetchProfile(session.user.id)
      } else {
        set({ user: null, profile: null })
      }
    })
  },

  fetchProfile: async (userId) => {
    const { data } = await supabase
      .from('profiles')
      .select('*')
      .eq('id', userId)
      .single()
    if (data) set({ profile: data })
  },

  signIn: async (email, password) => {
    const { data, error } = await supabase.auth.signInWithPassword({ email, password })
    if (error) throw error
    return data
  },

  signOut: async () => {
    await supabase.auth.signOut()
    set({ user: null, profile: null })
    // clear respondent/lawyer session
    sessionStorage.removeItem('respondent_session')
    sessionStorage.removeItem('lawyer_session')
  },

  // Respondent/Lawyer session (token-based, stored in sessionStorage)
  setRespondentSession: (data) => sessionStorage.setItem('respondent_session', JSON.stringify(data)),
  getRespondentSession: () => {
    try { return JSON.parse(sessionStorage.getItem('respondent_session')) } catch { return null }
  },
  clearRespondentSession: () => sessionStorage.removeItem('respondent_session'),

  setLawyerSession: (data) => sessionStorage.setItem('lawyer_session', JSON.stringify(data)),
  getLawyerSession: () => {
    try { return JSON.parse(sessionStorage.getItem('lawyer_session')) } catch { return null }
  },
  clearLawyerSession: () => sessionStorage.removeItem('lawyer_session'),
}))
