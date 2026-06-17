import { useState, useEffect, FormEvent } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { ArrowLeft, Save } from 'lucide-react'
import toast from 'react-hot-toast'
import { employeeService } from '../../services/crudService'

const departments = ['Administrativo', 'Financeiro', 'RH', 'Vendas', 'Compras', 'Estoque', 'Produção', 'TI', 'Marketing']
const states = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']

export default function EmployeeForm() {
  const { id } = useParams()
  const navigate = useNavigate()
  const isEditing = !!id
  const [form, setForm] = useState({
    first_name: '', last_name: '', email: '', phone: '', document: '',
    address: '', city: '', state: '', zipcode: '', birth_date: '',
    department: '', position: '', salary: '', hire_date: '', status: 'active',
  })
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    if (isEditing && id) {
      employeeService.getById(Number(id)).then((e) => {
        setForm({
          first_name: e.first_name, last_name: e.last_name, email: e.email, phone: e.phone || '',
          document: e.document || '', address: e.address || '', city: e.city || '', state: e.state || '',
          zipcode: e.zipcode || '', birth_date: e.birth_date || '', department: e.department || '',
          position: e.position || '', salary: e.salary?.toString() || '', hire_date: e.hire_date, status: e.status,
        })
      })
    }
  }, [id, isEditing])

  const update = (f: string, v: string) => setForm((p) => ({ ...p, [f]: v }))

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = { ...form, salary: parseFloat(form.salary) || 0 }
      if (isEditing) { await employeeService.update(Number(id), payload); toast.success('Funcionário atualizado') }
      else { await employeeService.create(payload); toast.success('Funcionário cadastrado') }
      navigate('/employees')
    } catch (err: any) { toast.error(err.response?.data?.message || 'Erro ao salvar') }
    finally { setLoading(false) }
  }

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <button onClick={() => navigate('/employees')} className="btn-secondary btn-sm"><ArrowLeft size={16} />Voltar</button>
        <h1 className="text-2xl font-bold">{isEditing ? 'Editar Funcionário' : 'Novo Funcionário'}</h1>
      </div>
      <form onSubmit={handleSubmit} className="max-w-3xl">
        <div className="card space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label className="label">Nome *</label><input className="input" value={form.first_name} onChange={(e) => update('first_name', e.target.value)} required /></div>
            <div><label className="label">Sobrenome *</label><input className="input" value={form.last_name} onChange={(e) => update('last_name', e.target.value)} required /></div>
            <div><label className="label">Email *</label><input type="email" className="input" value={form.email} onChange={(e) => update('email', e.target.value)} required /></div>
            <div><label className="label">Telefone</label><input className="input" value={form.phone} onChange={(e) => update('phone', e.target.value)} /></div>
            <div><label className="label">CPF</label><input className="input" value={form.document} onChange={(e) => update('document', e.target.value)} /></div>
            <div><label className="label">Data Nascimento</label><input type="date" className="input" value={form.birth_date} onChange={(e) => update('birth_date', e.target.value)} /></div>
            <div><label className="label">Departamento</label><select className="input" value={form.department} onChange={(e) => update('department', e.target.value)}><option value="">Selecione...</option>{departments.map((d) => <option key={d} value={d}>{d}</option>)}</select></div>
            <div><label className="label">Cargo</label><input className="input" value={form.position} onChange={(e) => update('position', e.target.value)} /></div>
            <div><label className="label">Salário (R$)</label><input type="number" step="0.01" min="0" className="input" value={form.salary} onChange={(e) => update('salary', e.target.value)} /></div>
            <div><label className="label">Data Contratação *</label><input type="date" className="input" value={form.hire_date} onChange={(e) => update('hire_date', e.target.value)} required /></div>
            <div className="md:col-span-2"><label className="label">Endereço</label><input className="input" value={form.address} onChange={(e) => update('address', e.target.value)} /></div>
            <div><label className="label">Cidade</label><input className="input" value={form.city} onChange={(e) => update('city', e.target.value)} /></div>
            <div><label className="label">Estado</label><select className="input" value={form.state} onChange={(e) => update('state', e.target.value)}><option value="">Selecione...</option>{states.map((s) => <option key={s} value={s}>{s}</option>)}</select></div>
            <div><label className="label">CEP</label><input className="input" value={form.zipcode} onChange={(e) => update('zipcode', e.target.value)} /></div>
            <div><label className="label">Status</label><select className="input" value={form.status} onChange={(e) => update('status', e.target.value)}><option value="active">Ativo</option><option value="inactive">Inativo</option><option value="terminated">Desligado</option></select></div>
          </div>
          <div className="flex gap-3 pt-4 border-t">
            <button type="submit" disabled={loading} className="btn-primary">{loading ? <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" /> : <Save size={18} />}{isEditing ? 'Atualizar' : 'Salvar'}</button>
            <button type="button" onClick={() => navigate('/employees')} className="btn-secondary">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  )
}
