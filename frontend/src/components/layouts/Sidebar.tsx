import { NavLink } from 'react-router-dom'
import {
  LayoutDashboard, Package, ShoppingCart, Users, Truck,
  DollarSign, PiggyBank, Building2, UserCircle, Clock, Calculator,
  ChevronDown, ChevronRight, LogOut, Menu, X
} from 'lucide-react'
import { useState } from 'react'
import { useAuth } from '../../contexts/AuthContext'

interface MenuItem {
  label: string
  icon: React.ReactNode
  to?: string
  children?: { label: string; to: string }[]
}

const menuItems: MenuItem[] = [
  { label: 'Dashboard', icon: <LayoutDashboard size={20} />, to: '/dashboard' },
  {
    label: 'Produtos', icon: <Package size={20} />,
    children: [
      { label: 'Produtos', to: '/products' },
      { label: 'Categorias', to: '/categories' },
    ],
  },
  { label: 'Clientes', icon: <Users size={20} />, to: '/customers' },
  { label: 'Fornecedores', icon: <Truck size={20} />, to: '/suppliers' },
  {
    label: 'Vendas', icon: <ShoppingCart size={20} />,
    children: [
      { label: 'Todas Vendas', to: '/sales' },
      { label: 'Nova Venda', to: '/sales/new' },
    ],
  },
  {
    label: 'Compras', icon: <DollarSign size={20} />,
    children: [
      { label: 'Todas Compras', to: '/purchases' },
      { label: 'Nova Compra', to: '/purchases/new' },
    ],
  },
  {
    label: 'Financeiro', icon: <PiggyBank size={20} />,
    children: [
      { label: 'Contas', to: '/accounts' },
      { label: 'Transações', to: '/transactions' },
    ],
  },
  {
    label: 'RH', icon: <Building2 size={20} />,
    children: [
      { label: 'Funcionários', to: '/employees' },
      { label: 'Ponto', to: '/attendance' },
      { label: 'Folha Pagamento', to: '/payroll' },
    ],
  },
]

export default function Sidebar() {
  const { user, logout } = useAuth()
  const [expanded, setExpanded] = useState<string[]>([])
  const [mobileOpen, setMobileOpen] = useState(false)

  const toggleExpand = (label: string) => {
    setExpanded((prev) =>
      prev.includes(label) ? prev.filter((l) => l !== label) : [...prev, label]
    )
  }

  const sidebarContent = (
    <div className="flex flex-col h-full">
      <div className="px-3 py-5 border-b border-gray-700">
        <h1 className="text-xl font-bold text-white">ERP Stocco</h1>
        <p className="text-xs text-gray-400">Sistema de Gestão</p>
      </div>

      <nav className="flex-1 overflow-y-auto px-3 py-4 space-y-1">
        {menuItems.map((item) => (
          <div key={item.label}>
            {item.to ? (
              <NavLink
                to={item.to}
                onClick={() => setMobileOpen(false)}
                className={({ isActive }) =>
                  `flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors ${
                    isActive
                      ? 'bg-primary-600 text-white'
                      : 'text-gray-300 hover:bg-gray-700 hover:text-white'
                  }`
                }
              >
                {item.icon}
                <span>{item.label}</span>
              </NavLink>
            ) : (
              <button
                onClick={() => toggleExpand(item.label)}
                className="flex items-center justify-between w-full px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-700 hover:text-white transition-colors"
              >
                <div className="flex items-center gap-3">
                  {item.icon}
                  <span>{item.label}</span>
                </div>
                {expanded.includes(item.label) ? <ChevronDown size={16} /> : <ChevronRight size={16} />}
              </button>
            )}
            {item.children && expanded.includes(item.label) && (
              <div className="ml-8 mt-1 space-y-1">
                {item.children.map((child) => (
                  <NavLink
                    key={child.to}
                    to={child.to}
                    onClick={() => setMobileOpen(false)}
                    className={({ isActive }) =>
                      `block px-3 py-2 rounded-lg text-sm transition-colors ${
                        isActive
                          ? 'bg-primary-600 text-white'
                          : 'text-gray-400 hover:bg-gray-700 hover:text-white'
                      }`
                    }
                  >
                    {child.label}
                  </NavLink>
                ))}
              </div>
            )}
          </div>
        ))}
      </nav>

      <div className="border-t border-gray-700 px-3 py-4">
        <div className="flex items-center gap-3 mb-3">
          <div className="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-medium shrink-0">
            {user?.name?.charAt(0).toUpperCase()}
          </div>
          <div className="flex-1 min-w-0">
            <p className="text-sm font-medium text-white truncate">{user?.name}</p>
            <p className="text-xs text-gray-400 truncate">{user?.email}</p>
          </div>
        </div>
        <button
          onClick={logout}
          className="flex items-center gap-2 w-full px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition-colors"
        >
          <LogOut size={16} />
          <span>Sair</span>
        </button>
      </div>
    </div>
  )

  return (
    <>
      <button
        onClick={() => setMobileOpen(true)}
        className="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-md"
      >
        <Menu size={20} />
      </button>

      {mobileOpen && (
        <div className="fixed inset-0 z-40 lg:hidden">
          <div className="fixed inset-0 bg-black/50" onClick={() => setMobileOpen(false)} />
          <div className="fixed left-0 top-0 h-full w-64 bg-gray-900 z-50">
            {sidebarContent}
          </div>
        </div>
      )}

      <aside className="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-gray-900">
        {sidebarContent}
      </aside>
    </>
  )
}
