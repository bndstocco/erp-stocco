import { useState, useEffect, FormEvent } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { ArrowLeft, Save } from 'lucide-react'
import toast from 'react-hot-toast'
import { productService, categoryService } from '../../services/crudService'
import { Category } from '../../types'

export default function ProductForm() {
  const { id } = useParams()
  const navigate = useNavigate()
  const isEditing = !!id

  const { data: categories } = useQuery({
    queryKey: ['categories-select'],
    queryFn: () => categoryService.getAll({ status: 'active' }),
  })

  const [form, setForm] = useState({
    name: '', description: '', sku: '', barcode: '',
    category_id: '', unit_price: '', cost_price: '',
    stock_quantity: '0', min_stock: '0', max_stock: '',
    unit: 'un', weight: '', status: 'active',
  })
  const [loading, setLoading] = useState(false)

  useEffect(() => {
    if (isEditing && id) {
      productService.getById(Number(id)).then((product) => {
        setForm({
          name: product.name || '',
          description: product.description || '',
          sku: product.sku || '',
          barcode: product.barcode || '',
          category_id: product.category_id?.toString() || '',
          unit_price: product.unit_price?.toString() || '',
          cost_price: product.cost_price?.toString() || '',
          stock_quantity: product.stock_quantity?.toString() || '0',
          min_stock: product.min_stock?.toString() || '0',
          max_stock: product.max_stock?.toString() || '',
          unit: product.unit || 'un',
          weight: product.weight?.toString() || '',
          status: product.status || 'active',
        })
      })
    }
  }, [id, isEditing])

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    setLoading(true)
    try {
      const payload = {
        ...form,
        unit_price: parseFloat(form.unit_price) || 0,
        cost_price: parseFloat(form.cost_price) || 0,
        stock_quantity: parseInt(form.stock_quantity) || 0,
        min_stock: parseInt(form.min_stock) || 0,
        max_stock: form.max_stock ? parseInt(form.max_stock) : undefined,
        category_id: form.category_id ? parseInt(form.category_id) : undefined,
        weight: form.weight ? parseFloat(form.weight) : undefined,
      }

      if (isEditing) {
        await productService.update(Number(id), payload)
        toast.success('Produto atualizado com sucesso')
      } else {
        await productService.create(payload)
        toast.success('Produto criado com sucesso')
      }
      navigate('/products')
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Erro ao salvar produto')
    } finally {
      setLoading(false)
    }
  }

  const updateField = (field: string, value: string) => setForm((prev) => ({ ...prev, [field]: value }))

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <button onClick={() => navigate('/products')} className="btn-secondary btn-sm">
          <ArrowLeft size={16} />
          Voltar
        </button>
        <h1 className="text-2xl font-bold text-gray-900">{isEditing ? 'Editar Produto' : 'Novo Produto'}</h1>
      </div>

      <form onSubmit={handleSubmit} className="max-w-3xl">
        <div className="card space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="md:col-span-2">
              <label className="label">Nome *</label>
              <input className="input" value={form.name} onChange={(e) => updateField('name', e.target.value)} required />
            </div>
            <div className="md:col-span-2">
              <label className="label">Descrição</label>
              <textarea className="input" rows={3} value={form.description} onChange={(e) => updateField('description', e.target.value)} />
            </div>
            <div>
              <label className="label">SKU *</label>
              <input className="input" value={form.sku} onChange={(e) => updateField('sku', e.target.value)} required />
            </div>
            <div>
              <label className="label">Código de Barras</label>
              <input className="input" value={form.barcode} onChange={(e) => updateField('barcode', e.target.value)} />
            </div>
            <div>
              <label className="label">Categoria</label>
              <select className="input" value={form.category_id} onChange={(e) => updateField('category_id', e.target.value)}>
                <option value="">Selecione...</option>
                {categories?.map((cat: Category) => (
                  <option key={cat.id} value={cat.id}>{cat.name}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="label">Unidade</label>
              <select className="input" value={form.unit} onChange={(e) => updateField('unit', e.target.value)}>
                <option value="un">Unidade</option>
                <option value="kg">Quilograma</option>
                <option value="g">Grama</option>
                <option value="l">Litro</option>
                <option value="ml">Mililitro</option>
                <option value="m">Metro</option>
                <option value="cx">Caixa</option>
                <option value="pc">Pacote</option>
              </select>
            </div>
            <div>
              <label className="label">Preço de Venda *</label>
              <input type="number" step="0.01" min="0" className="input" value={form.unit_price} onChange={(e) => updateField('unit_price', e.target.value)} required />
            </div>
            <div>
              <label className="label">Preço de Custo *</label>
              <input type="number" step="0.01" min="0" className="input" value={form.cost_price} onChange={(e) => updateField('cost_price', e.target.value)} required />
            </div>
            <div>
              <label className="label">Estoque Atual</label>
              <input type="number" min="0" className="input" value={form.stock_quantity} onChange={(e) => updateField('stock_quantity', e.target.value)} />
            </div>
            <div>
              <label className="label">Estoque Mínimo</label>
              <input type="number" min="0" className="input" value={form.min_stock} onChange={(e) => updateField('min_stock', e.target.value)} />
            </div>
            <div>
              <label className="label">Estoque Máximo</label>
              <input type="number" min="0" className="input" value={form.max_stock} onChange={(e) => updateField('max_stock', e.target.value)} />
            </div>
            <div>
              <label className="label">Peso (kg)</label>
              <input type="number" step="0.001" min="0" className="input" value={form.weight} onChange={(e) => updateField('weight', e.target.value)} />
            </div>
            <div>
              <label className="label">Status</label>
              <select className="input" value={form.status} onChange={(e) => updateField('status', e.target.value)}>
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </select>
            </div>
          </div>

          <div className="flex gap-3 pt-4 border-t">
            <button type="submit" disabled={loading} className="btn-primary">
              {loading ? <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" /> : <Save size={18} />}
              {isEditing ? 'Atualizar' : 'Salvar'}
            </button>
            <button type="button" onClick={() => navigate('/products')} className="btn-secondary">Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  )
}
