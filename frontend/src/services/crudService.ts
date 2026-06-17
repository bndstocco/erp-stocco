import api from './api'
import { ApiResponse, PaginatedResponse } from '../types'

export function createCrudService<T>(endpoint: string) {
  return {
    async list(params?: Record<string, any>): Promise<PaginatedResponse<T>> {
      const response = await api.get<ApiResponse<PaginatedResponse<T>>>(endpoint, { params })
      return response.data.data as PaginatedResponse<T>
    },

    async getById(id: number): Promise<T> {
      const response = await api.get<ApiResponse<T>>(`${endpoint}/${id}`)
      return response.data.data as T
    },

    async create(payload: Partial<T>): Promise<T> {
      const response = await api.post<ApiResponse<T>>(endpoint, payload)
      return response.data.data as T
    },

    async update(id: number, payload: Partial<T>): Promise<T> {
      const response = await api.put<ApiResponse<T>>(`${endpoint}/${id}`, payload)
      return response.data.data as T
    },

    async delete(id: number): Promise<void> {
      await api.delete(`${endpoint}/${id}`)
    },

    async getAll(params?: Record<string, any>): Promise<T[]> {
      const response = await api.get<ApiResponse<any>>(endpoint, { params })
      const data = response.data.data
      if (data && typeof data === 'object' && 'data' in data && Array.isArray(data.data)) {
        return data.data as T[]
      }
      return (data || []) as T[]
    },
  }
}

export const productService = createCrudService<import('../types').Product>('/products')
export const categoryService = createCrudService<import('../types').Category>('/categories')
export const customerService = createCrudService<import('../types').Customer>('/customers')
export const supplierService = createCrudService<import('../types').Supplier>('/suppliers')
export const saleService = createCrudService<import('../types').Sale>('/sales')
export const purchaseService = createCrudService<import('../types').Purchase>('/purchases')
export const accountService = createCrudService<import('../types').Account>('/accounts')
export const transactionService = createCrudService<import('../types').Transaction>('/transactions')
export const employeeService = createCrudService<import('../types').Employee>('/employees')
export const attendanceService = createCrudService<import('../types').Attendance>('/attendance')
export const payrollService = createCrudService<import('../types').Payroll>('/payroll')
