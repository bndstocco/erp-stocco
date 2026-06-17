import { useQuery } from '@tanstack/react-query'
import { useState } from 'react'
import { useNavigate } from 'react-router-dom'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import { payrollService } from '../../services/crudService'
import { Payroll } from '../../types'
import { formatCurrency, formatDate } from '../../utils/format'

export default function PayrollList() {
  const navigate = useNavigate()
  const [page, setPage] = useState(1)

  const { data, isLoading } = useQuery({
    queryKey: ['payroll', page],
    queryFn: () => payrollService.list({ page, per_page: 15 }),
  })

  const columns = [
    { key: 'employee_name', header: 'Funcionário', render: (p: Payroll) => p.employee_name || '#' + p.employee_id },
    { key: 'period_start', header: 'Período', render: (p: Payroll) => `${formatDate(p.period_start)} - ${formatDate(p.period_end)}` },
    { key: 'gross_salary', header: 'Bruto', render: (p: Payroll) => formatCurrency(p.gross_salary) },
    { key: 'net_salary', header: 'Líquido', render: (p: Payroll) => <span className="font-semibold">{formatCurrency(p.net_salary)}</span> },
    { key: 'status', header: 'Status', render: (p: Payroll) => <StatusBadge status={p.status} /> },
    { key: 'payment_date', header: 'Pagamento', render: (p: Payroll) => p.payment_date ? formatDate(p.payment_date) : '-' },
  ]

  return (
    <div>
      <PageHeader title="Folha de Pagamento" subtitle="Gerenciamento de salários" buttonLabel="Nova Folha" buttonPath="/payroll/new" />
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>
    </div>
  )
}
