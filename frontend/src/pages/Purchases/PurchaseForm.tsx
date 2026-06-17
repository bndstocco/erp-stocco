import { useState, FormEvent } from 'react'
import { useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import { ArrowLeft, Save, Plus, Trash2, Search } from 'lucide-react'
import toast from 'react-hot-toast'
import { productService, supplierService, purchaseService } from '../../services/crudService'
import { Product, Supplier, PurchaseItem } from '../../types'
import { formatCurrency } from '../../utils/format'
import Modal from '../../components/ui/Modal'

export default function PurchaseForm() {
  const navigate = useNavigate()

  const { data: products } = useQuery({ queryKey: ['products-all'], queryFn: () => productService.getAll({ status: 'active', per_page: 1000 }) })
  const { data: suppliers } = useQuery({ queryKey: ['suppliers-all'], queryFn: () => supplierService.getAll({ status: 'active', per_page: 1000 }) })

  const [form, setForm] = useState({ supplier_id: '', discount: '0', notes: '' })
  const [items, setItems] = useState<PurchaseItem[]>([])
  const [productModal, setProductModal] = useState(false)
  const [productSearch, setProductSearch] = useState('')

  const subtotal = items.reduce((sum, i) => sum + i.subtotal, 0)
  const discount = parseFloat(form.discount) || 0
  const total = subtotal - discount

  function addProduct(product: Product) {
    const existing = items.find((i) => i.product_id === product.id)
    if (existing) {
      setItems((prev) => prev.map((i) =>
        i.product_id === product.id
          ? { ...i, quantity: i.quantity + 1, subtotal: (i.quantity + 1) * i.unit_price }
          : i
      ))
    } else {
      setItems((prev) => [...prev, {
        product_id: product.id, product_name: product.name,
        quantity: 1, unit_price: product.cost_price, subtotal: product.cost_price,
      }])
    }
    setProductModal(false)
  }

  function removeItem(index: number) { setItems((prev) => prev.filter((_, i) => i !== index)) }
  function updateQuantity(index: number, qty: number) {
    setItems((prev) => prev.map((item, i) =>
      i === index ? { ...item, quantity: qty, subtotal: qty * item.unit_price } : item
    ))
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    if (items.length === 0) { toast.error('Adicione pelo menos um item'); return }
    try {
      await purchaseService.create({
        supplier_id: form.supplier_id ? parseInt(form.supplier_id) : undefined,
        discount, notes: form.notes,
        items: items.map(({ product_id, product_name, quantity, unit_price }) => ({
          product_id, product_name, quantity, unit_price,
          subtotal: quantity * unit_price,
        })),
      })
      toast.success('Compra registrada!')
      navigate('/purchases')
    } catch (err: any) { toast.error(err.response?.data?.message || 'Erro ao salvar') }
  }

  const filteredProducts = Array.isArray(products) ? products.filter((p: Product) =>
    p.name.toLowerCase().includes(productSearch.toLowerCase()) || p.sku.toLowerCase().includes(productSearch.toLowerCase())
  ) : []

  return (
    <div>
      <div className="flex items-center gap-4 mb-6">
        <button onClick={() => navigate('/purchases')} className="btn-secondary btn-sm"><ArrowLeft size={16} />Voltar</button>
        <h1 className="text-2xl font-bold">Nova Compra</h1>
      </div>
      <form onSubmit={handleSubmit}>
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div className="lg:col-span-2 space-y-6">
            <div className="card">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-lg font-semibold">Itens da Compra</h2>
                <button type="button" onClick={() => setProductModal(true)} className="btn-primary btn-sm"><Plus size={16} /> Adicionar Produto</button>
              </div>
              {items.length === 0 ? <p className="text-center py-8 text-gray-500">Nenhum item adicionado</p> : (
                <div className="overflow-x-auto">
                  <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                      <tr><th className="table-header">Produto</th><th className="table-header">Preço Custo</th><th className="table-header">Qtd</th><th className="table-header">Subtotal</th><th className="table-header"></th></tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                      {items.map((item, index) => (
                        <tr key={index}>
                          <td className="table-cell font-medium">{item.product_name}</td>
                          <td className="table-cell">{formatCurrency(item.unit_price)}</td>
                          <td className="table-cell"><input type="number" min="1" className="input w-20" value={item.quantity} onChange={(e) => updateQuantity(index, parseInt(e.target.value) || 1)} /></td>
                          <td className="table-cell font-semibold">{formatCurrency(item.subtotal)}</td>
                          <td className="table-cell"><button type="button" onClick={() => removeItem(index)} className="text-red-600"><Trash2 size={16} /></button></td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </div>
          </div>
          <div className="space-y-6">
            <div className="card space-y-4">
              <h2 className="text-lg font-semibold">Detalhes</h2>
              <div>
                <label className="label">Fornecedor</label>
                <select className="input" value={form.supplier_id} onChange={(e) => setForm({ ...form, supplier_id: e.target.value })}>
                  <option value="">Selecione...</option>
                  {suppliers?.map((s: Supplier) => <option key={s.id} value={s.id}>{s.company_name}</option>)}
                </select>
              </div>
              <div>
                <label className="label">Desconto (R$)</label>
                <input type="number" step="0.01" min="0" className="input" value={form.discount} onChange={(e) => setForm({ ...form, discount: e.target.value })} />
              </div>
              <div>
                <label className="label">Observações</label>
                <textarea className="input" rows={3} value={form.notes} onChange={(e) => setForm({ ...form, notes: e.target.value })} />
              </div>
            </div>
            <div className="card space-y-3">
              <div className="flex justify-between text-sm"><span>Subtotal:</span><span>{formatCurrency(subtotal)}</span></div>
              <div className="flex justify-between text-sm"><span>Desconto:</span><span>- {formatCurrency(discount)}</span></div>
              <div className="flex justify-between text-lg font-bold border-t pt-2"><span>Total:</span><span>{formatCurrency(total)}</span></div>
              <button type="submit" className="btn-primary w-full py-3 mt-2"><Save size={18} />Registrar Compra</button>
            </div>
          </div>
        </div>
      </form>

      <Modal isOpen={productModal} onClose={() => setProductModal(false)} title="Adicionar Produto">
        <div className="space-y-3">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
            <input className="input pl-10" placeholder="Buscar produto..." value={productSearch} onChange={(e) => setProductSearch(e.target.value)} />
          </div>
          <div className="max-h-80 overflow-y-auto space-y-2">
            {filteredProducts.map((product: Product) => (
              <button key={product.id} type="button" onClick={() => addProduct(product)}
                className="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-gray-200 text-left">
                <div>
                  <p className="font-medium text-sm">{product.name}</p>
                  <p className="text-xs text-gray-500">SKU: {product.sku}</p>
                </div>
                <span className="text-sm font-semibold">{formatCurrency(product.cost_price)}</span>
              </button>
            ))}
          </div>
        </div>
      </Modal>
    </div>
  )
}
