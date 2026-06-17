import { ChevronLeft, ChevronRight, Search } from 'lucide-react'
import { useState } from 'react'

interface Column<T> {
  key: string
  header: string
  render?: (item: T) => React.ReactNode
  sortable?: boolean
}

interface DataTableProps<T> {
  columns: Column<T>[]
  data: T[]
  loading?: boolean
  onSearch?: (value: string) => void
  onPageChange?: (page: number) => void
  currentPage?: number
  lastPage?: number
  total?: number
  from?: number
  to?: number
}

export default function DataTable<T extends Record<string, any>>({
  columns,
  data,
  loading,
  onSearch,
  onPageChange,
  currentPage = 1,
  lastPage = 1,
  total = 0,
  from = 0,
  to = 0,
}: DataTableProps<T>) {
  const [searchValue, setSearchValue] = useState('')

  const handleSearch = (value: string) => {
    setSearchValue(value)
    onSearch?.(value)
  }

  return (
    <div>
      {onSearch && (
        <div className="mb-4">
          <div className="relative max-w-md">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
            <input
              type="text"
              placeholder="Buscar..."
              value={searchValue}
              onChange={(e) => handleSearch(e.target.value)}
              className="input pl-10"
            />
          </div>
        </div>
      )}

      <div className="overflow-x-auto rounded-lg border border-gray-200">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              {columns.map((col) => (
                <th key={col.key} className="table-header">
                  {col.header}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {loading ? (
              <tr>
                <td colSpan={columns.length} className="px-4 py-12 text-center text-gray-500">
                  <div className="flex items-center justify-center gap-2">
                    <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-primary-600" />
                    Carregando...
                  </div>
                </td>
              </tr>
            ) : data.length === 0 ? (
              <tr>
                <td colSpan={columns.length} className="px-4 py-12 text-center text-gray-500">
                  Nenhum registro encontrado
                </td>
              </tr>
            ) : (
              data.map((item, index) => (
                <tr key={item.id || index} className="hover:bg-gray-50 transition-colors">
                  {columns.map((col) => (
                    <td key={col.key} className="table-cell">
                      {col.render ? col.render(item) : item[col.key]}
                    </td>
                  ))}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {total > 0 && onPageChange && (
        <div className="flex items-center justify-between mt-4">
          <p className="text-sm text-gray-600">
            Mostrando {from} a {to} de {total} registros
          </p>
          <div className="flex items-center gap-2">
            <button
              onClick={() => onPageChange(currentPage - 1)}
              disabled={currentPage === 1}
              className="btn-secondary btn-sm"
            >
              <ChevronLeft size={16} />
            </button>
            <span className="text-sm text-gray-600">
              Página {currentPage} de {lastPage}
            </span>
            <button
              onClick={() => onPageChange(currentPage + 1)}
              disabled={currentPage === lastPage}
              className="btn-secondary btn-sm"
            >
              <ChevronRight size={16} />
            </button>
          </div>
        </div>
      )}
    </div>
  )
}
