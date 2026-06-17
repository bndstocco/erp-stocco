import { useQuery } from '@tanstack/react-query'
import { Building2, PiggyBank, DollarSign, CreditCard, TrendingUp } from 'lucide-react'
import PageHeader from '../../components/ui/PageHeader'
import StatusBadge from '../../components/ui/StatusBadge'
import { accountService } from '../../services/crudService'
import { Account } from '../../types'
import { formatCurrency } from '../../utils/format'

const typeIcons: Record<string, React.ReactNode> = {
  checking: <Building2 size={24} />,
  savings: <PiggyBank size={24} />,
  cash: <DollarSign size={24} />,
  credit_card: <CreditCard size={24} />,
  investment: <TrendingUp size={24} />,
  other: <DollarSign size={24} />,
}

const typeLabels: Record<string, string> = {
  checking: 'Conta Corrente', savings: 'Conta Poupança', cash: 'Caixa',
  credit_card: 'Cartão de Crédito', investment: 'Investimento', other: 'Outro',
}

export default function AccountList() {
  const { data, isLoading } = useQuery({
    queryKey: ['accounts'],
    queryFn: () => accountService.getAll(),
  })

  const totalBalance = Array.isArray(data) ? data.reduce((sum: number, a: Account) => sum + a.balance, 0) : 0

  return (
    <div>
      <PageHeader title="Contas Financeiras" subtitle="Gerencie suas contas bancárias e saldos" />

      <div className="card mb-6">
        <div className="flex items-center justify-between">
          <p className="text-sm text-gray-500">Saldo Total</p>
          <p className="text-3xl font-bold text-gray-900">{formatCurrency(totalBalance)}</p>
        </div>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        {isLoading ? (
          <div className="col-span-full flex justify-center py-12"><div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600" /></div>
        ) : data?.length === 0 ? (
          <div className="col-span-full text-center py-12 text-gray-500">Nenhuma conta cadastrada</div>
        ) : (
          data?.map((account: Account) => (
            <div key={account.id} className="card">
              <div className="flex items-center justify-between mb-4">
                <div className={`p-2 rounded-lg ${account.balance >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'}`}>
                  {typeIcons[account.type] || <DollarSign size={24} />}
                </div>
                <StatusBadge status={account.status} />
              </div>
              <h3 className="font-semibold text-gray-900">{account.name}</h3>
              <p className="text-sm text-gray-500">{typeLabels[account.type] || account.type}</p>
              {account.bank && <p className="text-xs text-gray-400 mt-1">{account.bank} {account.agency ? `- Ag ${account.agency}` : ''} {account.account_number ? `- CC ${account.account_number}` : ''}</p>}
              <p className={`text-2xl font-bold mt-3 ${account.balance >= 0 ? 'text-green-600' : 'text-red-600'}`}>
                {formatCurrency(account.balance)}
              </p>
            </div>
          ))
        )}
      </div>
    </div>
  )
}
