import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Edit, Trash2, Mail, Phone } from 'lucide-react'
import toast from 'react-hot-toast'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { customerService } from '../../services/crudService'
import { Customer } from '../../types'

export default function CustomerList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['customers', page, search],
    queryFn: () => customerService.list({ page, search, per_page: 15 }),
  })

  async function handleDelete(id: number) {
    if (!confirm('Excluir cliente?')) return
    try {
      await customerService.delete(id)
      toast.success('Cliente excluído')
      refetch()
    } catch { toast.error('Erro ao excluir') }
  }

  const columns = [
    { key: 'name', header: 'Nome', render: (c: Customer) => <span className="font-medium">{c.name}</span> },
    { key: 'email', header: 'Contato', render: (c: Customer) => (
      <div className="space-y-1">
        {c.email && <div className="flex items-center gap-1 text-sm"><Mail size={14} /><span>{c.email}</span></div>}
        {c.phone && <div className="flex items-center gap-1 text-sm"><Phone size={14} /><span>{c.phone}</span></div>}
      </div>
    )},
    { key: 'document', header: 'Documento' },
    { key: 'city', header: 'Cidade', render: (c: Customer) => c.city ? `${c.city}/${c.state || ''}` : '-' },
    { key: 'status', header: 'Status', render: (c: Customer) => <StatusBadge status={c.status} /> },
    { key: 'actions', header: 'Ações', render: (c: Customer) => (
      <div className="flex gap-2">
        <button onClick={() => navigate(`/customers/${c.id}/edit`)} className="text-primary-600 hover:text-primary-800"><Edit size={16} /></button>
        <button onClick={() => handleDelete(c.id)} className="text-red-600 hover:text-red-800"><Trash2 size={16} /></button>
      </div>
    )},
  ]

  return (
    <div>
      <PageHeader title="Clientes" subtitle="Gerencie sua base de clientes" buttonLabel="Novo Cliente" buttonPath="/customers/new" />
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading} onSearch={setSearch}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>
    </div>
  )
}
