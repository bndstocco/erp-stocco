import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { Edit, Trash2, User, Mail, Phone, Building2 } from 'lucide-react'
import toast from 'react-hot-toast'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { employeeService } from '../../services/crudService'
import { Employee } from '../../types'
import { formatCurrency } from '../../utils/format'

export default function EmployeeList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)
  const [search, setSearch] = useState('')

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['employees', page, search],
    queryFn: () => employeeService.list({ page, search, per_page: 15 }),
  })

  async function handleDelete(id: number) {
    if (!confirm('Excluir funcionário?')) return
    try { await employeeService.delete(id); toast.success('Funcionário excluído'); refetch() }
    catch { toast.error('Erro ao excluir') }
  }

  const columns = [
    { key: 'full_name', header: 'Funcionário', render: (e: Employee) => (
      <div className="flex items-center gap-2">
        <div className="w-8 h-8 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center text-sm font-medium">{e.first_name?.charAt(0)}{e.last_name?.charAt(0)}</div>
        <div><p className="font-medium">{e.full_name}</p><p className="text-xs text-gray-500">{e.position || '-'}</p></div>
      </div>
    )},
    { key: 'email', header: 'Contato', render: (e: Employee) => (
      <div className="space-y-1 text-sm">
        {e.email && <div className="flex items-center gap-1"><Mail size={14} /><span>{e.email}</span></div>}
        {e.phone && <div className="flex items-center gap-1"><Phone size={14} /><span>{e.phone}</span></div>}
      </div>
    )},
    { key: 'department', header: 'Departamento' },
    { key: 'salary', header: 'Salário', render: (e: Employee) => formatCurrency(e.salary) },
    { key: 'status', header: 'Status', render: (e: Employee) => <StatusBadge status={e.status} /> },
    { key: 'actions', header: 'Ações', render: (e: Employee) => (
      <div className="flex gap-2">
        <button onClick={() => navigate(`/employees/${e.id}/edit`)} className="text-primary-600"><Edit size={16} /></button>
        <button onClick={() => handleDelete(e.id)} className="text-red-600"><Trash2 size={16} /></button>
      </div>
    )},
  ]

  return (
    <div>
      <PageHeader title="Funcionários" subtitle="Gerencie sua equipe" buttonLabel="Novo Funcionário" buttonPath="/employees/new" />
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading} onSearch={setSearch}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>
    </div>
  )
}
