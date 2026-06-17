import { useQuery } from '@tanstack/react-query'
import { Package, ShoppingCart, Users, PiggyBank, Building2, AlertTriangle, TrendingUp, DollarSign } from 'lucide-react'
import { BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from 'recharts'
import api from '../../services/api'
import { DashboardData, ApiResponse } from '../../types'
import StatsCard from '../../components/ui/StatsCard'
import StatusBadge from '../../components/ui/StatusBadge'
import { formatCurrency, formatDate } from '../../utils/format'
import { useNavigate } from 'react-router-dom'

export default function Dashboard() {
  const navigate = useNavigate()
  const { data, isLoading } = useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => {
      const { data } = await api.get<ApiResponse<DashboardData>>('/dashboard')
      return data.data
    },
  })

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600" />
      </div>
    )
  }

  const summary = data?.summary
  const chartData = data?.monthly_revenue_chart?.map((item) => ({
    month: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'][item.month - 1],
    receita: item.revenue,
  })) || []

  return (
    <div>
      <h1 className="text-2xl font-bold text-gray-900 mb-6">Dashboard</h1>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <StatsCard
          title="Produtos"
          value={summary?.total_products || 0}
          icon={<Package size={24} />}
          subtitle="Total cadastrados"
          color="blue"
        />
        <StatsCard
          title="Vendas"
          value={summary?.total_sales || 0}
          icon={<ShoppingCart size={24} />}
          subtitle="Vendas concluídas"
          color="green"
        />
        <StatsCard
          title="Clientes"
          value={summary?.total_customers || 0}
          icon={<Users size={24} />}
          subtitle="Clientes ativos"
          color="purple"
        />
        <StatsCard
          title="Saldo Total"
          value={formatCurrency(summary?.total_balance || 0)}
          icon={<PiggyBank size={24} />}
          color="primary"
        />
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <StatsCard
          title="Funcionários"
          value={summary?.total_employees || 0}
          icon={<Building2 size={24} />}
          color="primary"
        />
        <StatsCard
          title="Receita do Mês"
          value={formatCurrency(summary?.monthly_revenue || 0)}
          icon={<TrendingUp size={24} />}
          color="green"
        />
        <StatsCard
          title="Produtos Estoque Baixo"
          value={summary?.low_stock_products || 0}
          icon={<AlertTriangle size={24} />}
          subtitle="Abaixo do mínimo"
          color="red"
        />
        <StatsCard
          title="Ticket Médio"
          value={formatCurrency(
            summary?.total_sales ? (summary?.monthly_revenue || 0) / summary.total_sales : 0
          )}
          icon={<DollarSign size={24} />}
          color="yellow"
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 card">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Receita Mensal</h2>
          <div className="h-80">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={chartData}>
                <CartesianGrid strokeDasharray="3 3" stroke="#f0f0f0" />
                <XAxis dataKey="month" stroke="#9ca3af" fontSize={12} />
                <YAxis stroke="#9ca3af" fontSize={12} tickFormatter={(v) => `R$${v/1000}k`} />
                <Tooltip formatter={(value: number) => formatCurrency(value)} />
                <Bar dataKey="receita" fill="#3b82f6" radius={[4, 4, 0, 0]} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="card">
          <h2 className="text-lg font-semibold text-gray-900 mb-4">Produtos Mais Vendidos</h2>
          <div className="space-y-3">
            {data?.top_products?.map((product, idx) => (
              <div key={product.product_id || idx} className="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-medium text-gray-900 truncate">{product.product_name}</p>
                  <p className="text-xs text-gray-500">{product.total_quantity} unidades vendidas</p>
                </div>
                <p className="text-sm font-semibold text-gray-900 ml-4">
                  {formatCurrency(product.total_revenue)}
                </p>
              </div>
            ))}
            {(!data?.top_products || data.top_products.length === 0) && (
              <p className="text-sm text-gray-500 text-center py-4">Nenhuma venda ainda</p>
            )}
          </div>
        </div>
      </div>

      <div className="mt-6 card">
        <div className="flex items-center justify-between mb-4">
          <h2 className="text-lg font-semibold text-gray-900">Últimas Vendas</h2>
          <button onClick={() => navigate('/sales')} className="text-sm text-primary-600 hover:text-primary-700 font-medium">
            Ver todas
          </button>
        </div>
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="table-header">NF</th>
                <th className="table-header">Cliente</th>
                <th className="table-header">Valor</th>
                <th className="table-header">Status</th>
                <th className="table-header">Data</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-200">
              {data?.recent_sales?.map((sale: any) => (
                <tr key={sale.id} className="hover:bg-gray-50 cursor-pointer" onClick={() => navigate(`/sales/${sale.id}`)}>
                  <td className="table-cell font-medium">{sale.invoice_number}</td>
                  <td className="table-cell">{sale.customer_name || 'Consumidor'}</td>
                  <td className="table-cell">{formatCurrency(sale.total)}</td>
                  <td className="table-cell"><StatusBadge status={sale.status} /></td>
                  <td className="table-cell">{formatDate(sale.sale_date)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}
