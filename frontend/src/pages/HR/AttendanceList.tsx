import { useQuery } from '@tanstack/react-query'
import { useState, FormEvent } from 'react'
import { Plus } from 'lucide-react'
import toast from 'react-hot-toast'
import PageHeader from '../../components/ui/PageHeader'
import DataTable from '../../components/ui/DataTable'
import StatusBadge from '../../components/ui/StatusBadge'
import Modal from '../../components/ui/Modal'
import { attendanceService, employeeService } from '../../services/crudService'
import { Attendance, Employee } from '../../types'

export default function AttendanceList() {
  const [page, setPage] = useState(1)
  const [modalOpen, setModalOpen] = useState(false)
  const [form, setForm] = useState({ employee_id: '', date: new Date().toISOString().split('T')[0], check_in: '08:00', check_out: '18:00', lunch_start: '12:00', lunch_end: '13:00', status: 'present', notes: '' })

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['attendance', page],
    queryFn: () => attendanceService.list({ page, per_page: 15 }),
  })

  const { data: employees } = useQuery({
    queryKey: ['employees-active'],
    queryFn: () => employeeService.getAll({ status: 'active' }),
  })

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    try {
      await attendanceService.create({ ...form, employee_id: parseInt(form.employee_id) })
      toast.success('Registro salvo')
      setModalOpen(false)
      refetch()
    } catch (err: any) { toast.error(err.response?.data?.message || 'Erro ao salvar') }
  }

  const columns = [
    { key: 'employee_name', header: 'Funcionário', render: (a: Attendance) => a.employee_name || '#' + a.employee_id },
    { key: 'date', header: 'Data' },
    { key: 'check_in', header: 'Entrada' },
    { key: 'check_out', header: 'Saída' },
    { key: 'hours_worked', header: 'Horas', render: (a: Attendance) => `${a.hours_worked}h` },
    { key: 'overtime', header: 'HE', render: (a: Attendance) => a.overtime > 0 ? `${a.overtime}h` : '-' },
    { key: 'status', header: 'Status', render: (a: Attendance) => <StatusBadge status={a.status} /> },
  ]

  return (
    <div>
      <PageHeader title="Ponto Eletrônico" subtitle="Registro de frequência dos funcionários">
        <button onClick={() => setModalOpen(true)} className="btn-primary"><Plus size={18} />Novo Registro</button>
      </PageHeader>
      <div className="card">
        <DataTable columns={columns} data={data?.data || []} loading={isLoading}
          onPageChange={setPage} currentPage={data?.current_page} lastPage={data?.last_page}
          total={data?.total} from={data?.from} to={data?.to} />
      </div>

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title="Registrar Ponto">
        <form onSubmit={handleSubmit} className="space-y-4">
          <div><label className="label">Funcionário *</label>
            <select className="input" value={form.employee_id} onChange={(e) => setForm({ ...form, employee_id: e.target.value })} required>
              <option value="">Selecione...</option>
              {employees?.map((e: Employee) => <option key={e.id} value={e.id}>{e.full_name}</option>)}
            </select>
          </div>
          <div><label className="label">Data *</label><input type="date" className="input" value={form.date} onChange={(e) => setForm({ ...form, date: e.target.value })} required /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><label className="label">Entrada</label><input type="time" className="input" value={form.check_in} onChange={(e) => setForm({ ...form, check_in: e.target.value })} /></div>
            <div><label className="label">Saída</label><input type="time" className="input" value={form.check_out} onChange={(e) => setForm({ ...form, check_out: e.target.value })} /></div>
            <div><label className="label">Almoço Início</label><input type="time" className="input" value={form.lunch_start} onChange={(e) => setForm({ ...form, lunch_start: e.target.value })} /></div>
            <div><label className="label">Almoço Fim</label><input type="time" className="input" value={form.lunch_end} onChange={(e) => setForm({ ...form, lunch_end: e.target.value })} /></div>
          </div>
          <div><label className="label">Status</label>
            <select className="input" value={form.status} onChange={(e) => setForm({ ...form, status: e.target.value })}>
              <option value="present">Presente</option><option value="absent">Ausente</option>
              <option value="late">Atrasado</option><option value="half_day">Meio Período</option>
              <option value="vacation">Férias</option><option value="sick_leave">Atestado</option>
            </select>
          </div>
          <div><label className="label">Observações</label><textarea className="input" rows={2} value={form.notes} onChange={(e) => setForm({ ...form, notes: e.target.value })} /></div>
          <div className="flex gap-3 pt-2">
            <button type="submit" className="btn-primary"><Plus size={16} />Registrar</button>
            <button type="button" onClick={() => setModalOpen(false)} className="btn-secondary">Cancelar</button>
          </div>
        </form>
      </Modal>
    </div>
  )
}
