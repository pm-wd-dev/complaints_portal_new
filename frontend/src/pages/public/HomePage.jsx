import { Link } from 'react-router-dom'
import PublicLayout from '@/components/layout/PublicLayout'
import { FileText, Search, Clock, CheckCircle, Shield } from 'lucide-react'

const steps = [
  {
    icon: FileText,
    step: '01',
    title: 'Submit',
    desc: 'File your complaint with all necessary details. Anonymous submissions are fully supported.',
  },
  {
    icon: Clock,
    step: '02',
    title: 'Review',
    desc: 'Your complaint is reviewed and assigned to the appropriate respondent and legal team.',
  },
  {
    icon: CheckCircle,
    step: '03',
    title: 'Resolve',
    desc: 'Track updates at every stage until your complaint reaches a fair and final resolution.',
  },
]

export default function HomePage() {
  return (
    <PublicLayout>

      {/* Hero */}
      <section className="bg-blue-600 text-white">
        <div className="max-w-6xl mx-auto px-6 py-20">
          <p className="text-blue-200 text-xs font-semibold tracking-widest uppercase mb-4">
            Complaint Management System
          </p>
          <h1 className="text-4xl sm:text-5xl font-bold leading-tight tracking-tight max-w-2xl mb-5">
            Every complaint handled fairly and transparently.
          </h1>
          <p className="text-blue-100 text-base max-w-xl mb-10 leading-relaxed">
            Submit and track your complaints securely. We ensure every case is reviewed with confidentiality and care.
          </p>
          <div className="flex flex-col sm:flex-row gap-3">
            <Link
              to="/submit"
              className="inline-flex items-center justify-center gap-2 bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold text-sm hover:bg-blue-50 transition-colors"
            >
              <FileText size={16} />
              Submit a Complaint
            </Link>
            <Link
              to="/track"
              className="inline-flex items-center justify-center gap-2 border border-blue-400 text-white px-6 py-3 rounded-lg font-semibold text-sm hover:bg-blue-500 transition-colors"
            >
              <Search size={16} />
              Track Your Complaint
            </Link>
          </div>
        </div>
      </section>

      {/* How it works */}
      <section className="max-w-6xl mx-auto px-6 py-16">
        <h2 className="text-xl font-bold text-gray-900 mb-10">How it works</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {steps.map(({ icon: Icon, step, title, desc }) => (
            <div key={step} className="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
              <div className="flex items-center gap-3 mb-4">
                <span className="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">{step}</span>
                <h3 className="font-semibold text-gray-900">{title}</h3>
              </div>
              <p className="text-gray-500 text-sm leading-relaxed">{desc}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Privacy banner */}
      <section className="max-w-6xl mx-auto px-6 pb-16">
        <div className="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex items-start gap-4">
          <div className="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
            <Shield size={20} className="text-blue-600" />
          </div>
          <div>
            <p className="font-semibold text-gray-900 mb-1">Your privacy is protected</p>
            <p className="text-gray-500 text-sm leading-relaxed">
              All complaints are handled with strict confidentiality. You may choose to submit anonymously — your identity will never be shared without consent.
            </p>
          </div>
        </div>
      </section>

    </PublicLayout>
  )
}
