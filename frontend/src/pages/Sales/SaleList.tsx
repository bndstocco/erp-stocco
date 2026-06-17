import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Eye } from 'lucide-react'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { saleService } from '../../services/crudService'
import { Sale } from '../../types'
import { formatCurrency, formatDate } from '../../utils/format'

export default function SaleList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['sales', page, search],
    queryFn: () => saleService.list({ page, search, per_page: 15 }),
  })

  const columns = [
    { key: 'invoice_number', header: 'NF', render: (s: Sale) => <span className="font-mono text-xs font-medium">{s.invoice_number}</span> },
    { key: 'customer_name', header: 'Cliente', render: (s: Sale) => s.customer_name || 'Consumidor' },
    { key: 'total', header: 'Valor', render: (s: Sale) => <span className="font-semibold">{formatCurrency(s.total)}</span> },
    { key: 'payment_method', header: 'Pagamento', render: (s: Sale) => {
      const labels: Record<string, string> = { cash: 'Dinheiro', credit_card: 'Cartão Crédito', debit_card: 'Cartão Débito', pix: 'PIX', transfer: 'Transferência', boleto: 'Boleto', other: 'Outro' }
      return labels[s.payment_method] || s.payment_method
    }},
    { key: 'status', header: 'Status', render: (s: Sale) => <StatusBadge status={s.status} /> },
    { key: 'sale_date', header: 'Data', render: (s: Sale) => formatDate(s.sale_date || s.created_at || '') },
    { key: 'actions', header: '', render: (s: Sale) => (
      <button onClick={() => navigate(`/sales/${s.id}`)} className="text-primary-600 hover:text-primary-800"><Eye size={16} /></button>
    )},
  ]

  return (
    <div>
      <PageHeader title="Vendas" subtitle="Histórico de vendas realizadas" buttonLabel="Nova Venda" buttonPath="/sales/new" />
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading} onSearch={setSearch}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>
    </div>
  )
}
