import { ReactNode } from 'react'
import { Plus } from 'lucide-react'
import { useNavigate } from 'react-router-dom'

interface PageHeaderProps {
  title: string
  subtitle?: string
  buttonLabel?: string
  buttonPath?: string
  children?: ReactNode
}

export default function PageHeader({ title, subtitle, buttonLabel, buttonPath, children }: PageHeaderProps) {
  const navigate = useNavigate()

  return (
    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">{title}</h1>
        {subtitle && <p className="text-sm text-gray-500 mt-1">{subtitle}</p>}
      </div>
      <div className="flex items-center gap-2">
        {children}
        {buttonLabel && buttonPath && (
          <button onClick={() => navigate(buttonPath)} className="btn-primary">
            <Plus size={18} />
            {buttonLabel}
          </button>
        )}
      </div>
    </div>
  )
}
