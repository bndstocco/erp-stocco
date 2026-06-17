import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Edit, Trash2, AlertTriangle } from 'lucide-react'
import toast from 'react-hot-toast'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { productService } from '../../services/crudService'
import { Product } from '../../types'
import { formatCurrency } from '../../utils/format'

export default function ProductList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['products', page, search],
    queryFn: () => productService.list({ page, search, per_page: 15 }),
  })

  async function handleDelete(id: number) {
    if (!confirm('Tem certeza que deseja excluir este produto?')) return
    try {
      await productService.delete(id)
      toast.success('Produto excluído com sucesso')
      refetch()
    } catch {
      toast.error('Erro ao excluir produto')
    }
  }

  const columns = [
    { key: 'sku', header: 'SKU', render: (p: Product) => <span className="font-mono text-xs">{p.sku}</span> },
    { key: 'name', header: 'Produto', render: (p: Product) => (
      <div>
        <p className="font-medium">{p.name}</p>
        {p.category_name && <p className="text-xs text-gray-500">{p.category_name}</p>}
      </div>
    )},
    { key: 'unit_price', header: 'Preço', render: (p: Product) => formatCurrency(p.unit_price) },
    { key: 'stock_quantity', header: 'Estoque', render: (p: Product) => (
      <div className="flex items-center gap-1">
        <span className={p.is_low_stock ? 'text-red-600 font-medium' : ''}>{p.stock_quantity}</span>
        {p.is_low_stock && <AlertTriangle size={14} className="text-red-500" />}
      </div>
    )},
    { key: 'status', header: 'Status', render: (p: Product) => <StatusBadge status={p.status} /> },
    { key: 'actions', header: 'Ações', render: (p: Product) => (
      <div className="flex items-center gap-2">
        <button onClick={() => navigate(`/products/${p.id}/edit`)} className="text-primary-600 hover:text-primary-800">
          <Edit size={16} />
        </button>
        <button onClick={() => handleDelete(p.id)} className="text-red-600 hover:text-red-800">
          <Trash2 size={16} />
        </button>
      </div>
    )},
  ]

  return (
    <div>
      <PageHeader title="Produtos" subtitle="Gerencie seu catálogo de produtos" buttonLabel="Novo Produto" buttonPath="/products/new" />
      <div className="card">
        <DataTable
          columns={columns}
          data={data?.data || []}
          loading={isLoading}
          onSearch={setSearch}
          onPageChange={setPage}
          currentPage={data?.current_page}
          lastPage={data?.last_page}
          total={data?.total}
          from={data?.from}
          to={data?.to}
        />
      </div>
    </div>
  )
}
