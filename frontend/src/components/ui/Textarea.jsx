import { cn } from '@/lib/utils'

export default function Textarea({ label, error, className, rows = 4, ...props }) {
  return (
    <div className="flex flex-col gap-1">
      {label && <label className="text-sm font-medium text-gray-700">{label}</label>}
      <textarea
        rows={rows}
        className={cn(
          'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 resize-vertical',
          'focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500',
          error && 'border-red-400 focus:border-red-500',
          className
        )}
        {...props}
      />
      {error && <p className="text-xs text-red-600">{error}</p>}
    </div>
  )
}
