import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { purchaseService } from '../../services/crudService'
import { Purchase } from '../../types'
import { formatCurrency, formatDate } from '../../utils/format'

export default function PurchaseList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')

  const { data, isLoading } = useQuery({
    queryKey: ['purchases', page, search],
    queryFn: () => purchaseService.list({ page, search, per_page: 15 }),
  })

  const columns = [
    { key: 'purchase_order', header: 'Pedido', render: (p: Purchase) => <span className="font-mono text-xs font-medium">{p.purchase_order}</span> },
    { key: 'supplier_name', header: 'Fornecedor', render: (p: Purchase) => p.supplier_name || '-' },
    { key: 'total', header: 'Valor', render: (p: Purchase) => <span className="font-semibold">{formatCurrency(p.total)}</span> },
    { key: 'status', header: 'Status', render: (p: Purchase) => <StatusBadge status={p.status} /> },
    { key: 'purchase_date', header: 'Data', render: (p: Purchase) => formatDate(p.purchase_date || p.created_at || '') },
    { key: 'actions', header: '', render: (p: Purchase) => (
      <button onClick={() => navigate(`/purchases/${p.id}`)} className="text-primary-600 hover:text-primary-800 text-sm">Detalhes</button>
    )},
  ]

  return (
    <div>
      <PageHeader title="Compras" subtitle="Histórico de compras" buttonLabel="Nova Compra" buttonPath="/purchases/new" />
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading} onSearch={setSearch}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>
    </div>
  )
}
