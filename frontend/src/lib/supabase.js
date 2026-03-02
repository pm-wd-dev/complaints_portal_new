import { createClient } from '@supabase/supabase-js'

const supabaseUrl = import.meta.env.VITE_SUPABASE_URL
const supabaseAnonKey = import.meta.env.VITE_SUPABASE_ANON_KEY

if (!supabaseUrl || !supabaseAnonKey) {
  console.error(
    '[Supabase] VITE_SUPABASE_URL and VITE_SUPABASE_ANON_KEY are not set. ' +
    'All database requests will fail until these are configured.'
  )
}

export const supabase = createClient(
  supabaseUrl ?? 'http://localhost',
  supabaseAnonKey ?? 'not-configured'
)

/**
 * Translates any Supabase or network error into a human-readable message.
 * Use this in every catch block instead of showing err.message directly.
 */
export function getErrorMessage(err) {
  if (!err) return 'Something went wrong. Please try again.'

  const msg = (err.message || err.error_description || '').toLowerCase()
  const code = err.code || err.status || ''

  // Network / connectivity
  if (
    msg.includes('failed to fetch') ||
    msg.includes('networkerror') ||
    msg.includes('network request failed') ||
    msg.includes('load failed')
  ) {
    return 'Unable to connect. Please check your internet connection and try again.'
  }

  if (msg.includes('aborted') || msg.includes('timeout')) {
    return 'The request timed out. Please try again.'
  }

  if (msg.includes('service unavailable') || code === 503 || code === '503') {
    return 'The service is temporarily unavailable. Please try again in a few minutes.'
  }

  // Supabase / Postgres error codes
  if (code === '42501' || msg.includes('row-level security') || msg.includes('permission denied')) {
    return 'You do not have permission to perform this action.'
  }

  if (code === '23505' || msg.includes('duplicate') || msg.includes('already exists')) {
    return 'A duplicate entry was detected. Please try again.'
  }

  if (code === '23502' || msg.includes('not-null constraint') || msg.includes('null value in column')) {
    return 'A required field is missing. Please fill in all required fields.'
  }

  if (code === '22P02' || msg.includes('invalid input syntax')) {
    return 'One or more fields contain invalid data. Please check your inputs.'
  }

  if (code === '22001' || msg.includes('value too long')) {
    return 'One of your inputs is too long. Please shorten it and try again.'
  }

  // Supabase Auth errors
  if (msg.includes('invalid login credentials') || msg.includes('invalid email or password')) {
    return 'Invalid email or password. Please try again.'
  }

  if (msg.includes('email not confirmed')) {
    return 'Please confirm your email address before logging in.'
  }

  if (msg.includes('user already registered')) {
    return 'An account with this email already exists.'
  }

  if (msg.includes('jwt expired') || msg.includes('token is expired')) {
    return 'Your session has expired. Please log in again.'
  }

  // Fall back to the raw message only if it's readable
  const rawMsg = err.message || ''
  if (rawMsg && !rawMsg.toLowerCase().includes('typeerror') && rawMsg.length < 200) {
    return rawMsg
  }

  return 'Something went wrong. Please try again.'
}
