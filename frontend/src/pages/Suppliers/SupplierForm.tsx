import { useState, useEffect, FormEvent } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { ArrowLeft, Save } from 'lucide-react'
import toast from 'react-hot-toast'
import { supplierService } from '../../services/crudService'

const states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']

export default function SupplierForm() {
  const { id } = useParams()
  const navigate = useNavigate()
  const isEditing = !!id
  const [form, setForm] = useState({
    company_name: '', contact_name: '', email: '', phone: '', document: '',
    address: '', city: '', state: '', zipcode: '', website: '', notes: '', status: 'active',
  })
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    if (isEditing && id) {
      supplierService.getById(Number(id)).then((s) => {
        setForm({
          company_name: s.company_name, contact_name: s.contact_name || '', email: s.email || '',
          phone: s.phone || '', document: s.document || '', address: s.address || '',
          city: s.city || '', state: s.state || '', zipcode: s.zipcode || '',
          website: s.website || '', notes: s.notes || '', status: s.status,
        })
      })
    }
  }, [id, isEditing])

  const update = (f: string, v: string) => setForm((p) => ({ ...p, [f]: v }))

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setLoading(true)
    try {
      if (isEditing) { await supplierService.update(Number(id), form); toast.success('Fornecedor atualizado') }
      else { await supplierService.create(form); toast.success('Fornecedor criado') }
      navigate('/suppliers')
    } catch (err: any) { toast.error(err.response?.data?.message || 'Erro ao salvar') }
    finally { setLoading(false) }
  }

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <button onClick={() => navigate('/suppliers')} className="btn-secondary btn-sm"><ArrowLeft size={16} />Voltar</button>
        <h1 className="text-2xl font-bold">{isEditing ? 'Editar Fornecedor' : 'Novo Fornecedor'}</h1>
      </div>
      <form onSubmit={handleSubmit} className="max-w-3xl">
        <div className="card space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="md:col-span-2"><label className="label">Empresa *</label><input className="input" value={form.company_name} onChange={(e) => update('company_name', e.target.value)} required /></div>
            <div><label className="label">Nome Contato</label><input className="input" value={form.contact_name} onChange={(e) => update('contact_name', e.target.value)} /></div>
            <div><label className="label">Email</label><input type="email" className="input" value={form.email} onChange={(e) => update('email', e.target.value)} /></div>
            <div><label className="label">Telefone</label><input className="input" value={form.phone} onChange={(e) => update('phone', e.target.value)} /></div>
            <div><label className="label">CNPJ/CPF</label><input className="input" value={form.document} onChange={(e) => update('document', e.target.value)} /></div>
            <div><label className="label">Site</label><input className="input" value={form.website} onChange={(e) => update('website', e.target.value)} /></div>
            <div className="md:col-span-2"><label className="label">Endereço</label><input className="input" value={form.address} onChange={(e) => update('address', e.target.value)} /></div>
            <div><label className="label">Cidade</label><input className="input" value={form.city} onChange={(e) => update('city', e.target.value)} /></div>
            <div><label className="label">Estado</label><select className="input" value={form.state} onChange={(e) => update('state', e.target.value)}><option value="">Selecione...</option>{states.map((s) => <option key={s} value={s}>{s}</option>)}</select></div>
            <div><label className="label">CEP</label><input className="input" value={form.zipcode} onChange={(e) => update('zipcode', e.target.value)} /></div>
            <div className="md:col-span-2"><label className="label">Observações</label><textarea className="input" rows={3} value={form.notes} onChange={(e) => update('notes', e.target.value)} /></div>
            <div><label className="label">Status</label><select className="input" value={form.status} onChange={(e) => update('status', e.target.value)}><option value="active">Ativo</option><option value="inactive">Inativo</option></select></div>
          </div>
          <div className="flex gap-3 pt-4 border-t">
            <button type="submit" disabled={loading} className="btn-primary">{loading ? <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" /> : <Save size={18} />}{isEditing ? 'Atualizar' : 'Salvar'}</button>
            <button type="button" onClick={() => navigate('/suppliers')} className="btn-secondary">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  )
}
