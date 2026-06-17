import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { ArrowUpRight, ArrowDownRight, ArrowLeftRight, Plus } from 'lucide-react'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { transactionService } from '../../services/crudService'
import { Transaction } from '../../types'
import { formatCurrency, formatDate } from '../../utils/format'

export default function TransactionList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ['transactions', page],
    queryFn: () => transactionService.list({ page, per_page: 15 }),
  })

  const typeIcon: Record<string, React.ReactNode> = {
    income: <ArrowUpRight size={16} className="text-green-500" />,
    expense: <ArrowDownRight size={16} className="text-red-500" />,
    transfer: <ArrowLeftRight size={16} className="text-blue-500" />,
  }

  const columns = [
    { key: 'type', header: 'Tipo', render: (t: Transaction) => (
      <div className="flex items-center gap-2">
        {typeIcon[t.type]}
        <span className="text-xs capitalize">{t.type === 'income' ? 'Receita' : t.type === 'expense' ? 'Despesa' : 'Transferência'}</span>
      </div>
    )},
    { key: 'account_name', header: 'Conta' },
    { key: 'category', header: 'Categoria' },
    { key: 'amount', header: 'Valor', render: (t: Transaction) => (
      <span className={`font-semibold ${t.type === 'income' ? 'text-green-600' : t.type === 'expense' ? 'text-red-600' : 'text-blue-600'}`}>
        {t.type === 'expense' ? '-' : '+'}{formatCurrency(t.amount)}
      </span>
    )},
    { key: 'description', header: 'Descrição' },
    { key: 'status', header: 'Status', render: (t: Transaction) => <StatusBadge status={t.status} /> },
    { key: 'transaction_date', header: 'Data', render: (t: Transaction) => formatDate(t.transaction_date) },
  ]

  return (
    <div>
      <PageHeader title="Transações" subtitle="Histórico de transações financeiras">
        <button onClick={() => navigate('/transactions/new')} className="btn-primary"><Plus size={18} />Nova Transação</button>
      </PageHeader>
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>
    </div>
  )
}
