import { getStatusColor, translateStatus } from '../../utils/format'

interface StatusBadgeProps {
  status: string
}

export default function StatusBadge({ status }: StatusBadgeProps) {
  return (
    <span className={`badge ${getStatusColor(status)}`}>
      {translateStatus(status)}
    </span>
  )
}
