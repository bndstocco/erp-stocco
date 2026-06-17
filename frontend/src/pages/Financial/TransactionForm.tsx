import { useState, FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { ArrowLeft, Save } from 'lucide-react'
import toast from 'react-hot-toast'
import { accountService, transactionService } from '../../services/crudService'
import { Account } from '../../types'

export default function TransactionForm() {
  const navigate = useNavigate()
  const { data: accounts } = useQuery({ queryKey: ['accounts-active'], queryFn: () => accountService.getAll({ status: 'active' }) })

  const [form, setForm] = useState({
    account_id: '', type: 'income', category: '', amount: '',
    description: '', payment_method: 'cash', status: 'completed', transaction_date: new Date().toISOString().split('T')[0],
  })
  const [loading, setLoading] = useState(false)

  const update = (f: string, v: string) => setForm((p) => ({ ...p, [f]: v }))

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    if (!form.account_id || !form.amount) { toast.error('Conta e valor são obrigatórios'); return }
    setLoading(true)
    try {
      await transactionService.create({
        ...form, account_id: parseInt(form.account_id), amount: parseFloat(form.amount),
      })
      toast.success('Transação registrada')
      navigate('/transactions')
    } catch (err: any) { toast.error(err.response?.data?.message || 'Erro ao salvar') }
    finally { setLoading(false) }
  }

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <button onClick={() => navigate('/transactions')} className="btn-secondary btn-sm"><ArrowLeft size={16} />Voltar</button>
        <h1 className="text-2xl font-bold">Nova Transação</h1>
      </div>
      <form onSubmit={handleSubmit} className="max-w-2xl">
        <div className="card space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="label">Tipo *</label>
              <select className="input" value={form.type} onChange={(e) => update('type', e.target.value)}>
                <option value="income">Receita</option>
                <option value="expense">Despesa</option>
                <option value="transfer">Transferência</option>
              </select>
            </div>
            <div>
              <label className="label">Conta *</label>
              <select className="input" value={form.account_id} onChange={(e) => update('account_id', e.target.value)} required>
                <option value="">Selecione...</option>
                {accounts?.map((a: Account) => <option key={a.id} value={a.id}>{a.name}</option>)}
              </select>
            </div>
            <div>
              <label className="label">Categoria</label>
              <select className="input" value={form.category} onChange={(e) => update('category', e.target.value)}>
                <option value="">Selecione...</option>
                <option value="vendas">Vendas</option><option value="servicos">Serviços</option>
                <option value="salarios">Salários</option><option value="fornecedores">Fornecedores</option>
                <option value="impostos">Impostos</option><option value="aluguel">Aluguel</option>
                <option value="utilidades">Utilidades</option><option value="marketing">Marketing</option>
                <option value="outros">Outros</option>
              </select>
            </div>
            <div>
              <label className="label">Valor *</label>
              <input type="number" step="0.01" min="0" className="input" value={form.amount} onChange={(e) => update('amount', e.target.value)} required />
            </div>
            <div>
              <label className="label">Forma de Pagamento</label>
              <select className="input" value={form.payment_method} onChange={(e) => update('payment_method', e.target.value)}>
                <option value="cash">Dinheiro</option><option value="credit_card">Cartão de Crédito</option>
                <option value="debit_card">Cartão de Débito</option><option value="pix">PIX</option>
                <option value="transfer">Transferência</option><option value="boleto">Boleto</option>
              </select>
            </div>
            <div>
              <label className="label">Data</label>
              <input type="date" className="input" value={form.transaction_date} onChange={(e) => update('transaction_date', e.target.value)} />
            </div>
            <div className="md:col-span-2">
              <label className="label">Descrição</label>
              <textarea className="input" rows={3} value={form.description} onChange={(e) => update('description', e.target.value)} />
            </div>
            <div>
              <label className="label">Status</label>
              <select className="input" value={form.status} onChange={(e) => update('status', e.target.value)}>
                <option value="completed">Concluído</option><option value="pending">Pendente</option><option value="cancelled">Cancelado</option>
              </select>
            </div>
          </div>
          <div className="flex gap-3 pt-4 border-t">
            <button type="submit" disabled={loading} className="btn-primary">
              {loading ? <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" /> : <Save size={18} />}
              Registrar
            </button>
            <button type="button" onClick={() => navigate('/transactions')} className="btn-secondary">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  )
}
