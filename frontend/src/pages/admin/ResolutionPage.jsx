import { useEffect, useState, useRef } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import AdminLayout from '@/components/layout/AdminLayout'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/Card'
import Button from '@/components/ui/Button'
import Textarea from '@/components/ui/Textarea'
import Select from '@/components/ui/Select'
import Input from '@/components/ui/Input'
import Modal from '@/components/ui/Modal'
import { supabase } from '@/lib/supabase'
import { formatDate, formatDateTime } from '@/lib/utils'
import { ArrowLeft, FileText, Download, PenTool, CheckCircle } from 'lucide-react'
import toast from 'react-hot-toast'

export default function ResolutionPage() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [complaint, setComplaint] = useState(null)
  const [resolution, setResolution] = useState(null)
  const [signatures, setSignatures] = useState([])
  const [loading, setLoading] = useState(true)
  const [saving, setSaving] = useState(false)
  const [showSigModal, setShowSigModal] = useState(false)
  const [sigForm, setSigForm] = useState({ signer_name: '', signer_email: '', signer_role: 'complainant' })
  const [form, setForm] = useState({ resolution_text: '', template_type: 'standard' })
  const canvasRef = useRef(null)
  const [drawing, setDrawing] = useState(false)
  const [hasSignature, setHasSignature] = useState(false)

  useEffect(() => { load() }, [id])

  const load = async () => {
    setLoading(true)
    try {
      const [{ data: c }, { data: r }] = await Promise.all([
        supabase.from('complaints').select('*').eq('id', id).single(),
        supabase.from('case_resolutions').select('*').eq('complaint_id', id).order('created_at', { ascending: false }).limit(1).maybeSingle(),
      ])
      setComplaint(c)
      if (r) {
        setResolution(r)
        setForm({ resolution_text: r.resolution_text, template_type: r.template_type })
        const { data: sigs } = await supabase.from('case_signatures').select('*').eq('resolution_id', r.id)
        setSignatures(sigs || [])
      }
    } catch (err) {
      toast.error('Failed to load resolution data')
    } finally {
      setLoading(false)
    }
  }

  const saveResolution = async () => {
    if (!form.resolution_text.trim()) return toast.error('Resolution text required')
    setSaving(true)
    try {
      const { data: { user } } = await supabase.auth.getUser()
      if (resolution) {
        await supabase.from('case_resolutions').update({ ...form, updated_at: new Date().toISOString() }).eq('id', resolution.id)
        toast.success('Resolution updated')
      } else {
        const { data, error } = await supabase.from('case_resolutions').insert({
          complaint_id: id, ...form, generated_by: user?.id
        }).select().single()
        if (error) throw error
        toast.success('Resolution created')
      }
      load()
    } catch (err) {
      toast.error(err.message || 'Failed to save')
    } finally {
      setSaving(false)
    }
  }

  // Canvas signature drawing
  const getPos = (e) => {
    const rect = canvasRef.current.getBoundingClientRect()
    const src = e.touches ? e.touches[0] : e
    return { x: src.clientX - rect.left, y: src.clientY - rect.top }
  }

  const startDraw = (e) => {
    setDrawing(true)
    const ctx = canvasRef.current.getContext('2d')
    const { x, y } = getPos(e)
    ctx.beginPath()
    ctx.moveTo(x, y)
  }

  const draw = (e) => {
    if (!drawing) return
    if (e.cancelable) e.preventDefault()
    const ctx = canvasRef.current.getContext('2d')
    const { x, y } = getPos(e)
    ctx.lineWidth = 2
    ctx.lineCap = 'round'
    ctx.strokeStyle = '#1e40af'
    ctx.lineTo(x, y)
    ctx.stroke()
    setHasSignature(true)
  }

  const stopDraw = () => setDrawing(false)

  const clearCanvas = () => {
    const ctx = canvasRef.current.getContext('2d')
    ctx.clearRect(0, 0, canvasRef.current.width, canvasRef.current.height)
    setHasSignature(false)
  }

  const submitSignature = async () => {
    if (!sigForm.signer_name || !sigForm.signer_email) return toast.error('Name and email required')
    if (!resolution) return toast.error('Save resolution first')
    setSaving(true)
    try {
      let signaturePath = null
      if (hasSignature) {
        const blob = await new Promise(res => canvasRef.current.toBlob(res, 'image/png'))
        const path = `signatures/${resolution.id}/${Date.now()}.png`
        const { error } = await supabase.storage.from('signatures').upload(path, blob)
        if (!error) {
          const { data: { publicUrl } } = supabase.storage.from('signatures').getPublicUrl(path)
          signaturePath = publicUrl
        }
      }
      await supabase.from('case_signatures').insert({
        complaint_id: id, resolution_id: resolution.id,
        ...sigForm, signature_path: signaturePath,
        signed_at: new Date().toISOString(),
      })
      toast.success('Signature added')
      setShowSigModal(false)
      setSigForm({ signer_name: '', signer_email: '', signer_role: 'complainant' })
      clearCanvas()
      load()
    } catch (err) {
      toast.error(err.message || 'Failed to add signature')
    } finally {
      setSaving(false)
    }
  }

  const printResolution = () => {
    const content = `
      <html><head><title>Case Resolution - ${complaint?.case_number}</title>
      <style>body{font-family:Arial,sans-serif;max-width:800px;margin:40px auto;padding:20px;}h1{color:#1e40af;}table{width:100%;border-collapse:collapse;}td,th{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#f5f5f5;}.sig{border:1px solid #ddd;padding:10px;margin:10px 0;}</style>
      </head><body>
      <h1>Case Resolution</h1>
      <p><strong>Case #:</strong> ${complaint?.case_number}</p>
      <p><strong>Date:</strong> ${formatDate(new Date())}</p>
      <hr/>
      <h2>Resolution</h2>
      <p>${resolution?.resolution_text?.replace(/\n/g, '<br>') || ''}</p>
      <hr/>
      <h2>Signatures</h2>
      ${signatures.map(s => `
        <div class="sig">
          <p><strong>${s.signer_name}</strong> (${s.signer_role}) — ${s.signer_email}</p>
          <p>Signed: ${formatDateTime(s.signed_at)}</p>
          ${s.signature_path ? `<img src="${s.signature_path}" style="max-height:60px;border:1px solid #ddd;"/>` : '<p><em>No digital signature</em></p>'}
        </div>
      `).join('')}
      </body></html>
    `
    const w = window.open('', '_blank')
    w.document.write(content)
    w.document.close()
    w.print()
  }

  if (loading) return <AdminLayout><div className="p-10 text-center text-gray-400">Loading...</div></AdminLayout>

  return (
    <AdminLayout>
      <div className="mb-6 flex items-center gap-3">
        <Button variant="ghost" size="sm" onClick={() => navigate(`/admin/complaints/${id}`)}><ArrowLeft size={15}/> Back</Button>
        <div className="flex-1">
          <h1 className="text-xl font-bold text-gray-900">Case Resolution</h1>
          <p className="text-xs text-gray-400 font-mono">{complaint?.case_number}</p>
        </div>
        {resolution && (
          <Button variant="outline" onClick={printResolution}><Download size={14}/> Print / PDF</Button>
        )}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div className="lg:col-span-2 flex flex-col gap-5">
          {/* Resolution form */}
          <Card>
            <CardHeader><CardTitle>Resolution Details</CardTitle></CardHeader>
            <CardContent className="flex flex-col gap-4">
              <Select label="Template Type" value={form.template_type} onChange={e => setForm(f=>({...f,template_type:e.target.value}))}>
                <option value="standard">Standard</option>
                <option value="detailed">Detailed</option>
                <option value="summary">Summary</option>
              </Select>
              <Textarea
                label="Resolution Text *"
                value={form.resolution_text}
                onChange={e => setForm(f=>({...f,resolution_text:e.target.value}))}
                rows={10}
                placeholder="Describe the resolution, findings, and outcome of this complaint..."
              />
              <div className="flex justify-end">
                <Button onClick={saveResolution} loading={saving}>
                  {resolution ? 'Update Resolution' : 'Save Resolution'}
                </Button>
              </div>
            </CardContent>
          </Card>

          {/* Signatures */}
          <Card>
            <CardHeader>
              <div className="flex items-center justify-between">
                <CardTitle>Signatures ({signatures.length})</CardTitle>
                {resolution && (
                  <Button size="sm" onClick={() => setShowSigModal(true)}><PenTool size={13}/> Add Signature</Button>
                )}
              </div>
            </CardHeader>
            <CardContent>
              {!resolution && <p className="text-sm text-gray-400">Save the resolution first to add signatures</p>}
              {signatures.length === 0 && resolution && <p className="text-sm text-gray-400">No signatures yet</p>}
              <div className="flex flex-col gap-3">
                {signatures.map(s => (
                  <div key={s.id} className="flex items-start gap-3 border border-gray-100 rounded-lg p-3">
                    <CheckCircle size={16} className="text-green-500 mt-0.5 flex-shrink-0" />
                    <div className="flex-1">
                      <p className="text-sm font-medium text-gray-800">{s.signer_name}</p>
                      <p className="text-xs text-gray-500">{s.signer_email} · <span className="capitalize">{s.signer_role}</span></p>
                      <p className="text-xs text-gray-400">{formatDateTime(s.signed_at)}</p>
                    </div>
                    {s.signature_path && (
                      <img src={s.signature_path} alt="signature" className="h-12 border border-gray-200 rounded" />
                    )}
                  </div>
                ))}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Complaint summary */}
        <Card className="h-fit">
          <CardHeader><CardTitle>Complaint Summary</CardTitle></CardHeader>
          <CardContent className="text-sm flex flex-col gap-2">
            <div><p className="text-xs text-gray-400">Case #</p><p className="font-mono text-gray-800">{complaint?.case_number}</p></div>
            <div><p className="text-xs text-gray-400">Submitted</p><p>{formatDate(complaint?.created_at)}</p></div>
            {complaint?.name && <div><p className="text-xs text-gray-400">Complainant</p><p>{complaint.name}</p></div>}
            {complaint?.complainee_name && <div><p className="text-xs text-gray-400">Complained About</p><p>{complaint.complainee_name}</p></div>}
            <div className="pt-2 border-t border-gray-100">
              <p className="text-xs text-gray-400 mb-1">Description</p>
              <p className="text-gray-600 text-xs leading-relaxed line-clamp-6">{complaint?.description}</p>
            </div>
          </CardContent>
        </Card>
      </div>

      {/* Signature Modal */}
      <Modal open={showSigModal} onClose={() => setShowSigModal(false)} title="Add Signature" size="lg">
        <div className="flex flex-col gap-4">
          <div className="grid grid-cols-2 gap-4">
            <Input label="Signer Name *" value={sigForm.signer_name} onChange={e => setSigForm(f=>({...f,signer_name:e.target.value}))} />
            <Input label="Signer Email *" type="email" value={sigForm.signer_email} onChange={e => setSigForm(f=>({...f,signer_email:e.target.value}))} />
          </div>
          <Select label="Role" value={sigForm.signer_role} onChange={e => setSigForm(f=>({...f,signer_role:e.target.value}))}>
            <option value="complainant">Complainant</option>
            <option value="respondent">Respondent</option>
            <option value="leadership">Leadership / Admin</option>
          </Select>
          <div>
            <p className="text-sm font-medium text-gray-700 mb-2">Digital Signature (draw below)</p>
            <div className="border border-gray-300 rounded-lg overflow-hidden bg-gray-50">
              <canvas
                ref={canvasRef}
                width={560}
                height={150}
                className="cursor-crosshair w-full touch-none"
                onMouseDown={startDraw}
                onMouseMove={draw}
                onMouseUp={stopDraw}
                onMouseLeave={stopDraw}
                onTouchStart={startDraw}
                onTouchMove={draw}
                onTouchEnd={stopDraw}
              />
            </div>
            <button onClick={clearCanvas} className="text-xs text-gray-400 hover:text-gray-600 mt-1">Clear signature</button>
          </div>
          <div className="flex gap-3 justify-end">
            <Button variant="secondary" onClick={() => setShowSigModal(false)}>Cancel</Button>
            <Button onClick={submitSignature} loading={saving}>Submit Signature</Button>
          </div>
        </div>
      </Modal>
    </AdminLayout>
  )
}
