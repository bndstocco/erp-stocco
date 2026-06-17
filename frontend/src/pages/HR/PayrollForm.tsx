import { useState, FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { ArrowLeft, Save, Calculator } from 'lucide-react'
import toast from 'react-hot-toast'
import { employeeService, payrollService } from '../../services/crudService'
import { Employee } from '../../types'
import { formatCurrency } from '../../utils/format'

export default function PayrollForm() {
  const navigate = useNavigate()
  const { data: employees } = useQuery({ queryKey: ['employees-active'], queryFn: () => employeeService.getAll({ status: 'active' }) })

  const [form, setForm] = useState({
    employee_id: '', period_start: '', period_end: '', gross_salary: '',
    bonuses: '0', commissions: '0', overtime_pay: '0',
    inss: '0', irrf: '0', fgts: '0', other_deductions: '0',
    payment_method: 'transfer', status: 'pending', notes: '',
  })
  const [loading, setLoading] = useState(false)

  const update = (f: string, v: string) => setForm((p) => ({ ...p, [f]: v }))

  function calcDeductions() {
    const gross = parseFloat(form.gross_salary) || 0
    const inss = gross * 0.09
    const irrf = gross > 2259.20 ? gross * 0.075 : 0
    const fgts = gross * 0.08
    setForm((p) => ({ ...p, inss: inss.toFixed(2), irrf: irrf.toFixed(2), fgts: fgts.toFixed(2) }))
    toast.success('Deduções calculadas com base no salário bruto')
  }

  const gross = parseFloat(form.gross_salary) || 0
  const bonuses = parseFloat(form.bonuses) || 0
  const commissions = parseFloat(form.commissions) || 0
  const overtimePay = parseFloat(form.overtime_pay) || 0
  const inss = parseFloat(form.inss) || 0
  const irrf = parseFloat(form.irrf) || 0
  const fgts = parseFloat(form.fgts) || 0
  const otherDeductions = parseFloat(form.other_deductions) || 0
  const netSalary = gross + bonuses + commissions + overtimePay - inss - irrf - fgts - otherDeductions

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    if (!form.employee_id) { toast.error('Selecione um funcionário'); return }
    setLoading(true)
    try {
      await payrollService.create({
        ...form,
        employee_id: parseInt(form.employee_id),
        gross_salary: gross, bonuses, commissions, overtime_pay: overtimePay,
        inss, irrf, fgts, other_deductions: otherDeductions, net_salary: netSalary,
      })
      toast.success('Folha de pagamento gerada')
      navigate('/payroll')
    } catch (err: any) { toast.error(err.response?.data?.message || 'Erro ao salvar') }
    finally { setLoading(false) }
  }

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <button onClick={() => navigate('/payroll')} className="btn-secondary btn-sm"><ArrowLeft size={16} />Voltar</button>
        <h1 className="text-2xl font-bold">Nova Folha de Pagamento</h1>
      </div>
      <form onSubmit={handleSubmit} className="max-w-3xl">
        <div className="card space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label className="label">Funcionário *</label>
              <select className="input" value={form.employee_id} onChange={(e) => {
                const emp = employees?.find((em: Employee) => em.id === parseInt(e.target.value))
                setForm({ ...form, employee_id: e.target.value, gross_salary: emp?.salary?.toString() || '' })
              }} required>
                <option value="">Selecione...</option>
                {employees?.map((e: Employee) => <option key={e.id} value={e.id}>{e.full_name}</option>)}
              </select>
            </div>
            <div><label className="label">Período Início *</label><input type="date" className="input" value={form.period_start} onChange={(e) => update('period_start', e.target.value)} required /></div>
            <div><label className="label">Período Fim *</label><input type="date" className="input" value={form.period_end} onChange={(e) => update('period_end', e.target.value)} required /></div>
            <div><label className="label">Forma Pagamento</label>
              <select className="input" value={form.payment_method} onChange={(e) => update('payment_method', e.target.value)}>
                <option value="transfer">Transferência</option><option value="pix">PIX</option>
                <option value="cash">Dinheiro</option><option value="check">Cheque</option>
              </select>
            </div>
          </div>

          <div className="border-t pt-4">
            <h3 className="font-semibold text-gray-900 mb-4">Proventos</h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><label className="label">Salário Bruto</label><input type="number" step="0.01" min="0" className="input" value={form.gross_salary} onChange={(e) => update('gross_salary', e.target.value)} /></div>
              <div><label className="label">Bônus</label><input type="number" step="0.01" min="0" className="input" value={form.bonuses} onChange={(e) => update('bonuses', e.target.value)} /></div>
              <div><label className="label">Comissões</label><input type="number" step="0.01" min="0" className="input" value={form.commissions} onChange={(e) => update('commissions', e.target.value)} /></div>
              <div><label className="label">HE</label><input type="number" step="0.01" min="0" className="input" value={form.overtime_pay} onChange={(e) => update('overtime_pay', e.target.value)} /></div>
            </div>
          </div>

          <div className="border-t pt-4">
            <div className="flex items-center justify-between mb-4">
              <h3 className="font-semibold text-gray-900">Deduções</h3>
              <button type="button" onClick={calcDeductions} className="btn-secondary btn-sm"><Calculator size={14} />Calcular Automático</button>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div><label className="label">INSS</label><input type="number" step="0.01" min="0" className="input" value={form.inss} onChange={(e) => update('inss', e.target.value)} /></div>
              <div><label className="label">IRRF</label><input type="number" step="0.01" min="0" className="input" value={form.irrf} onChange={(e) => update('irrf', e.target.value)} /></div>
              <div><label className="label">FGTS</label><input type="number" step="0.01" min="0" className="input" value={form.fgts} onChange={(e) => update('fgts', e.target.value)} /></div>
              <div><label className="label">Outras Deduções</label><input type="number" step="0.01" min="0" className="input" value={form.other_deductions} onChange={(e) => update('other_deductions', e.target.value)} /></div>
            </div>
          </div>

          <div className="border-t pt-4">
            <div className="p-4 bg-gray-50 rounded-lg space-y-2">
              <div className="flex justify-between text-sm"><span>Total Proventos:</span><span>{formatCurrency(gross + bonuses + commissions + overtimePay)}</span></div>
              <div className="flex justify-between text-sm"><span>Total Deduções:</span><span className="text-red-600">- {formatCurrency(inss + irrf + fgts + otherDeductions)}</span></div>
              <div className="flex justify-between text-lg font-bold border-t pt-2"><span>Salário Líquido:</span><span className="text-green-600">{formatCurrency(netSalary)}</span></div>
            </div>
          </div>

          <div><label className="label">Observações</label><textarea className="input" rows={2} value={form.notes} onChange={(e) => update('notes', e.target.value)} /></div>

          <div className="flex gap-3 pt-4 border-t">
            <button type="submit" disabled={loading} className="btn-primary">{loading ? <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" /> : <Save size={18} />}Gerar Folha</button>
            <button type="button" onClick={() => navigate('/payroll')} className="btn-secondary">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  )
}
