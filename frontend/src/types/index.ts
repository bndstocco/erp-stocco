export interface User {
  id: number
  name: string
  email: string
  phone?: string
  avatar?: string
  role_id?: number
  role_name?: string
  status: string
  created_at?: string
  updated_at?: string
}

export interface Product {
  id: number
  name: string
  description?: string
  sku: string
  barcode?: string
  category_id?: number
  category_name?: string
  unit_price: number
  cost_price: number
  stock_quantity: number
  min_stock: number
  max_stock?: number
  unit: string
  weight?: number
  status: string
  is_low_stock: boolean
  profit_margin: number
  created_at?: string
  updated_at?: string
}

export interface Category {
  id: number
  name: string
  description?: string
  parent_id?: number
  status: string
  created_at?: string
  updated_at?: string
}

export interface Customer {
  id: number
  name: string
  email?: string
  phone?: string
  document?: string
  address?: string
  city?: string
  state?: string
  zipcode?: string
  birth_date?: string
  notes?: string
  status: string
  created_at?: string
  updated_at?: string
}

export interface Supplier {
  id: number
  company_name: string
  contact_name?: string
  email?: string
  phone?: string
  document?: string
  address?: string
  city?: string
  state?: string
  zipcode?: string
  website?: string
  notes?: string
  status: string
  created_at?: string
  updated_at?: string
}

export interface Sale {
  id: number
  invoice_number: string
  customer_id?: number
  customer_name?: string
  user_id: number
  user_name?: string
  subtotal: number
  discount: number
  total: number
  payment_method: string
  installment_count: number
  status: string
  notes?: string
  sale_date?: string
  items: SaleItem[]
  created_at?: string
  updated_at?: string
}

export interface SaleItem {
  id?: number
  sale_id?: number
  product_id?: number
  product_name: string
  quantity: number
  unit_price: number
  subtotal: number
}

export interface Purchase {
  id: number
  purchase_order: string
  supplier_id?: number
  supplier_name?: string
  user_id: number
  user_name?: string
  subtotal: number
  discount: number
  total: number
  status: string
  notes?: string
  purchase_date?: string
  items: PurchaseItem[]
  created_at?: string
  updated_at?: string
}

export interface PurchaseItem {
  id?: number
  purchase_id?: number
  product_id?: number
  product_name: string
  quantity: number
  unit_price: number
  subtotal: number
}

export interface Account {
  id: number
  name: string
  type: string
  balance: number
  bank?: string
  agency?: string
  account_number?: string
  description?: string
  status: string
  created_at?: string
  updated_at?: string
}

export interface Transaction {
  id: number
  account_id: number
  account_name?: string
  type: string
  category?: string
  amount: number
  description?: string
  destination_account_id?: number
  payment_method: string
  status: string
  transaction_date: string
  created_at?: string
  updated_at?: string
}

export interface Employee {
  id: number
  user_id?: number
  first_name: string
  last_name: string
  full_name: string
  email: string
  phone?: string
  document?: string
  address?: string
  city?: string
  state?: string
  zipcode?: string
  birth_date?: string
  department?: string
  position?: string
  salary: number
  hire_date: string
  termination_date?: string
  status: string
  created_at?: string
  updated_at?: string
}

export interface Attendance {
  id: number
  employee_id: number
  employee_name?: string
  date: string
  check_in?: string
  check_out?: string
  lunch_start?: string
  lunch_end?: string
  hours_worked: number
  overtime: number
  status: string
  notes?: string
  created_at?: string
  updated_at?: string
}

export interface Payroll {
  id: number
  employee_id: number
  employee_name?: string
  period_start: string
  period_end: string
  gross_salary: number
  bonuses: number
  commissions: number
  overtime_pay: number
  inss: number
  irrf: number
  fgts: number
  other_deductions: number
  net_salary: number
  payment_date?: string
  payment_method: string
  status: string
  notes?: string
  created_at?: string
  updated_at?: string
}

export interface DashboardData {
  summary: {
    total_products: number
    total_customers: number
    total_employees: number
    total_balance: number
    monthly_revenue: number
    total_sales: number
    low_stock_products: number
  }
  monthly_revenue_chart: { month: number; total_sales: number; revenue: number; total_discounts: number }[]
  top_products: { product_id: number; product_name: string; total_quantity: number; total_revenue: number }[]
  recent_sales: PaginatedResponse<Sale>
}

export interface PaginatedResponse<T> {
  data: T[]
  current_page: number
  per_page: number
  total: number
  last_page: number
  from: number
  to: number
}

export interface ApiResponse<T> {
  error: boolean
  message?: string
  data: T
}
