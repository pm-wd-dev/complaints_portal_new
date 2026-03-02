import { clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'

export function cn(...inputs) {
  return twMerge(clsx(inputs))
}

export function generateCaseNumber(type = 'standard') {
  const prefix = type === 'guest' ? 'COMPG' : type === 'cast' ? 'COMPC' : 'COMP'
  const timestamp = Date.now().toString(36).toUpperCase()
  const random = Math.random().toString(36).substring(2, 6).toUpperCase()
  return `${prefix}-${timestamp}-${random}`
}

export function formatDate(dateStr) {
  if (!dateStr) return 'N/A'
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric', month: 'long', day: 'numeric'
  })
}

export function formatDateTime(dateStr) {
  if (!dateStr) return 'N/A'
  return new Date(dateStr).toLocaleString('en-US', {
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

export function getStatusColor(status) {
  const colors = {
    submitted:    'bg-blue-100 text-blue-800',
    under_review: 'bg-yellow-100 text-yellow-800',
    escalated:    'bg-orange-100 text-orange-800',
    resolved:     'bg-green-100 text-green-800',
    closed:       'bg-gray-100 text-gray-800',
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

export function getStatusLabel(status) {
  const labels = {
    submitted:    'Submitted',
    under_review: 'Under Review',
    escalated:    'Escalated',
    resolved:     'Resolved',
    closed:       'Closed',
  }
  return labels[status] || status
}

export function truncate(str, len = 100) {
  if (!str) return ''
  return str.length > len ? str.substring(0, len) + '...' : str
}
