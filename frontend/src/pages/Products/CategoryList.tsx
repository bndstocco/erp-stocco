import { useQuery } from '@tanstack/react-query'
import { useState, FormEvent } from 'react'
import { Plus, Edit2, Trash2, Save } from 'lucide-react'
import toast from 'react-hot-toast'
import PageHeader from '../../components/ui/PageHeader'
import Modal from '../../components/ui/Modal'
import StatusBadge from '../../components/ui/StatusBadge'
import { categoryService } from '../../services/crudService'
import { Category } from '../../types'

export default function CategoryList() {
  const [modalOpen, setModalOpen] = useState(false)
  const [editingCategory, setEditingCategory] = useState<Category | null>(null)
  const [form, setForm] = useState({ name: '', description: '', status: 'active' })

  const { data, isLoading, refetch } = useQuery({
    queryKey: ['categories'],
    queryFn: () => categoryService.getAll(),
  })

  function openNew() {
    setEditingCategory(null)
    setForm({ name: '', description: '', status: 'active' })
    setModalOpen(true)
  }

  function openEdit(category: Category) {
    setEditingCategory(category)
    setForm({ name: category.name, description: category.description || '', status: category.status })
    setModalOpen(true)
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    try {
      if (editingCategory) {
        await categoryService.update(editingCategory.id, form)
        toast.success('Categoria atualizada')
      } else {
        await categoryService.create(form)
        toast.success('Categoria criada')
      }
      setModalOpen(false)
      refetch()
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Erro ao salvar')
    }
  }

  async function handleDelete(id: number) {
    if (!confirm('Excluir categoria?')) return
    try {
      await categoryService.delete(id)
      toast.success('Categoria excluída')
      refetch()
    } catch {
      toast.error('Erro ao excluir')
    }
  }

  return (
    <div>
      <PageHeader title="Categorias" subtitle="Organize seus produtos por categorias" buttonLabel="Nova Categoria" buttonPath="#" />
      <div className="flex items-center justify-end mb-4">
        <button onClick={openNew} className="btn-primary btn-sm">
          <Plus size={16} />
          Nova Categoria
        </button>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {isLoading ? (
          <div className="col-span-full flex justify-center py-12">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600" />
          </div>
        ) : data?.length === 0 ? (
          <div className="col-span-full text-center py-12 text-gray-500">Nenhuma categoria cadastrada</div>
        ) : (
          data?.map((cat: Category) => (
            <div key={cat.id} className="card flex items-center justify-between">
              <div>
                <p className="font-medium text-gray-900">{cat.name}</p>
                {cat.description && <p className="text-sm text-gray-500 mt-1">{cat.description}</p>}
                <div className="mt-2"><StatusBadge status={cat.status} /></div>
              </div>
              <div className="flex items-center gap-2">
                <button onClick={() => openEdit(cat)} className="text-primary-600 hover:text-primary-800"><Edit2 size={16} /></button>
                <button onClick={() => handleDelete(cat.id)} className="text-red-600 hover:text-red-800"><Trash2 size={16} /></button>
              </div>
            </div>
          ))
        )}
      </div>

      <Modal isOpen={modalOpen} onClose={() => setModalOpen(false)} title={editingCategory ? 'Editar Categoria' : 'Nova Categoria'}>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="label">Nome *</label>
            <input className="input" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
          </div>
          <div>
            <label className="label">Descrição</label>
            <textarea className="input" rows={3} value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />
          </div>
          <div>
            <label className="label">Status</label>
            <select className="input" value={form.status} onChange={(e) => setForm({ ...form, status: e.target.value })}>
              <option value="active">Ativo</option>
              <option value="inactive">Inativo</option>
            </select>
          </div>
          <div className="flex gap-3 pt-2">
            <button type="submit" className="btn-primary"><Save size={16} />Salvar</button>
            <button type="button" onClick={() => setModalOpen(false)} className="btn-secondary">Cancelar</button>
          </div>
        </form>
      </Modal>
    </div>
  )
}
