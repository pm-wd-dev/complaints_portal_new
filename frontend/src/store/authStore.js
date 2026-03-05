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
    try {
      const { data: { session } } = await supabase.auth.getSession()
      if (session?.user) {
        set({ user: session.user })
        await get().fetchProfile(session.user.id)
      }
    } catch {
      // Supabase unreachable or not configured — continue as unauthenticated
    } finally {
      set({ loading: false })
    }

    supabase.auth.onAuthStateChange(async (_event, session) => {
      try {
        if (session?.user) {
          set({ user: session.user })
          await get().fetchProfile(session.user.id)
        } else {
          set({ user: null, profile: null })
        }
      } catch {
        // ignore auth state change errors
      }
    })
  },

  fetchProfile: async (userId) => {
    try {
      const { data } = await supabase
        .from('profiles')
        .select('*')
        .eq('id', userId)
        .maybeSingle()
      if (data) set({ profile: data })
    } catch {
      // ignore — user continues without profile
    }
  },

  signIn: async (email, password) => {
    const { data, error } = await supabase.auth.signInWithPassword({ email, password })
    if (error) throw error
    // Fetch profile immediately after sign-in so role is available
    if (data?.user) {
      set({ user: data.user })
      await get().fetchProfile(data.user.id)
    }
    return data
  },

  signOut: async () => {
    try {
      await supabase.auth.signOut()
    } catch {
      // ignore signout errors
    }
    set({ user: null, profile: null })
  },

  // Helper to check role
  hasRole: (role) => {
    const profile = get().profile
    return profile?.role === role
  },
}))
