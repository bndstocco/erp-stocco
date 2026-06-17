import { useState, useEffect, FormEvent } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { ArrowLeft, Save } from 'lucide-react'
import toast from 'react-hot-toast'
import { customerService } from '../../services/crudService'

const states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']

export default function CustomerForm() {
  const { id } = useParams()
  const navigate = useNavigate()
  const isEditing = !!id

  const [form, setForm] = useState({
    name: '', email: '', phone: '', document: '', address: '',
    city: '', state: '', zipcode: '', birth_date: '', notes: '', status: 'active',
  })
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    if (isEditing && id) {
      customerService.getById(Number(id)).then((c) => {
        setForm({
          name: c.name, email: c.email || '', phone: c.phone || '', document: c.document || '',
          address: c.address || '', city: c.city || '', state: c.state || '', zipcode: c.zipcode || '',
          birth_date: c.birth_date || '', notes: c.notes || '', status: c.status,
        })
      })
    }
  }, [id, isEditing])

  const update = (f: string, v: string) => setForm((p) => ({ ...p, [f]: v }))

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setLoading(true)
    try {
      if (isEditing) {
        await customerService.update(Number(id), form)
        toast.success('Cliente atualizado')
      } else {
        await customerService.create(form)
        toast.success('Cliente criado')
      }
      navigate('/customers')
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Erro ao salvar')
    } finally { setLoading(false) }
  }

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <button onClick={() => navigate('/customers')} className="btn-secondary btn-sm"><ArrowLeft size={16} />Voltar</button>
        <h1 className="text-2xl font-bold">{isEditing ? 'Editar Cliente' : 'Novo Cliente'}</h1>
      </div>
      <form onSubmit={handleSubmit} className="max-w-3xl">
        <div className="card space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="md:col-span-2"><label className="label">Nome *</label><input className="input" value={form.name} onChange={(e) => update('name', e.target.value)} required /></div>
            <div><label className="label">Email</label><input type="email" className="input" value={form.email} onChange={(e) => update('email', e.target.value)} /></div>
            <div><label className="label">Telefone</label><input className="input" value={form.phone} onChange={(e) => update('phone', e.target.value)} /></div>
            <div><label className="label">CPF/CNPJ</label><input className="input" value={form.document} onChange={(e) => update('document', e.target.value)} /></div>
            <div><label className="label">Data Nascimento</label><input type="date" className="input" value={form.birth_date} onChange={(e) => update('birth_date', e.target.value)} /></div>
            <div className="md:col-span-2"><label className="label">Endereço</label><input className="input" value={form.address} onChange={(e) => update('address', e.target.value)} /></div>
            <div><label className="label">Cidade</label><input className="input" value={form.city} onChange={(e) => update('city', e.target.value)} /></div>
            <div><label className="label">Estado</label><select className="input" value={form.state} onChange={(e) => update('state', e.target.value)}><option value="">Selecione...</option>{states.map((s) => <option key={s} value={s}>{s}</option>)}</select></div>
            <div><label className="label">CEP</label><input className="input" value={form.zipcode} onChange={(e) => update('zipcode', e.target.value)} /></div>
            <div className="md:col-span-2"><label className="label">Observações</label><textarea className="input" rows={3} value={form.notes} onChange={(e) => update('notes', e.target.value)} /></div>
            <div><label className="label">Status</label><select className="input" value={form.status} onChange={(e) => update('status', e.target.value)}><option value="active">Ativo</option><option value="inactive">Inativo</option></select></div>
          </div>
          <div className="flex gap-3 pt-4 border-t">
            <button type="submit" disabled={loading} className="btn-primary">{loading ? <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" /> : <Save size={18} />}{isEditing ? 'Atualizar' : 'Salvar'}</button>
            <button type="button" onClick={() => navigate('/customers')} className="btn-secondary">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  )
}
