import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Edit, Trash2, Building2 } from 'lucide-react'
import toast from 'react-hot-toast'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { supplierService } from '../../services/crudService'
import { Supplier } from '../../types'

export default function SupplierList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['suppliers', page, search],
    queryFn: () => supplierService.list({ page, search, per_page: 15 }),
  })

  async function handleDelete(id: number) {
    if (!confirm('Excluir fornecedor?')) return
    try { await supplierService.delete(id); toast.success('Fornecedor excluído'); refetch() }
    catch { toast.error('Erro ao excluir') }
  }

  const columns = [
    { key: 'company_name', header: 'Empresa', render: (s: Supplier) => (
      <div className="flex items-center gap-2"><Building2 size={16} className="text-gray-400" /><span className="font-medium">{s.company_name}</span></div>
    )},
    { key: 'contact_name', header: 'Contato' },
    { key: 'email', header: 'Email' },
    { key: 'phone', header: 'Telefone' },
    { key: 'status', header: 'Status', render: (s: Supplier) => <StatusBadge status={s.status} /> },
    { key: 'actions', header: 'Ações', render: (s: Supplier) => (
      <div className="flex gap-2">
        <button onClick={() => navigate(`/suppliers/${s.id}/edit`)} className="text-primary-600"><Edit size={16} /></button>
        <button onClick={() => handleDelete(s.id)} className="text-red-600"><Trash2 size={16} /></button>
      </div>
    )},
  ]

  return (
    <div>
      <PageHeader title="Fornecedores" subtitle="Gerencie seus fornecedores" buttonLabel="Novo Fornecedor" buttonPath="/suppliers/new" />
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading} onSearch={setSearch}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>
    </div>
  )
}
