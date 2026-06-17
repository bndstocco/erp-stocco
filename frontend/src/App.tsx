import { Routes, Route, Navigate } from 'react-router-dom'
import { useAuth } from './contexts/AuthContext'
import Layout from './components/layouts/Layout'
import Login from './pages/Auth/Login'
import Register from './pages/Auth/Register'
import Dashboard from './pages/Dashboard/Dashboard'
import ProductList from './pages/Products/ProductList'
import ProductForm from './pages/Products/ProductForm'
import CategoryList from './pages/Products/CategoryList'
import CustomerList from './pages/Customers/CustomerList'
import CustomerForm from './pages/Customers/CustomerForm'
import SupplierList from './pages/Suppliers/SupplierList'
import SupplierForm from './pages/Suppliers/SupplierForm'
import SaleList from './pages/Sales/SaleList'
import SaleForm from './pages/Sales/SaleForm'
import PurchaseList from './pages/Purchases/PurchaseList'
import PurchaseForm from './pages/Purchases/PurchaseForm'
import AccountList from './pages/Financial/AccountList'
import TransactionList from './pages/Financial/TransactionList'
import TransactionForm from './pages/Financial/TransactionForm'
import EmployeeList from './pages/HR/EmployeeList'
import EmployeeForm from './pages/HR/EmployeeForm'
import AttendanceList from './pages/HR/AttendanceList'
import PayrollList from './pages/HR/PayrollList'
import PayrollForm from './pages/HR/PayrollForm'

function PrivateRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated, loading } = useAuth()
  if (loading) return <div className="flex items-center justify-center h-screen"><div className="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600" /></div>
  return isAuthenticated ? <>{children}</> : <Navigate to="/login" />
}

export default function App() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/" element={<PrivateRoute><Layout /></PrivateRoute>}>
        <Route index element={<Navigate to="/dashboard" replace />} />
        <Route path="dashboard" element={<Dashboard />} />

        <Route path="products" element={<ProductList />} />
        <Route path="products/new" element={<ProductForm />} />
        <Route path="products/:id/edit" element={<ProductForm />} />
        <Route path="categories" element={<CategoryList />} />

        <Route path="customers" element={<CustomerList />} />
        <Route path="customers/new" element={<CustomerForm />} />
        <Route path="customers/:id/edit" element={<CustomerForm />} />

        <Route path="suppliers" element={<SupplierList />} />
        <Route path="suppliers/new" element={<SupplierForm />} />
        <Route path="suppliers/:id/edit" element={<SupplierForm />} />

        <Route path="sales" element={<SaleList />} />
        <Route path="sales/new" element={<SaleForm />} />
        <Route path="sales/:id" element={<SaleForm />} />

        <Route path="purchases" element={<PurchaseList />} />
        <Route path="purchases/new" element={<PurchaseForm />} />
        <Route path="purchases/:id" element={<PurchaseForm />} />

        <Route path="accounts" element={<AccountList />} />
        <Route path="transactions" element={<TransactionList />} />
        <Route path="transactions/new" element={<TransactionForm />} />

        <Route path="employees" element={<EmployeeList />} />
        <Route path="employees/new" element={<EmployeeForm />} />
        <Route path="employees/:id/edit" element={<EmployeeForm />} />
        <Route path="attendance" element={<AttendanceList />} />
        <Route path="payroll" element={<PayrollList />} />
        <Route path="payroll/new" element={<PayrollForm />} />
      </Route>
    </Routes>
  )
}
