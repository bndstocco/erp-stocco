import { useState, FormEvent } from 'react'
import { useNavigate, Link } from 'react-router-dom'
import { useAuth } from '../../contexts/AuthContext'
import { Building2, UserPlus, Eye, EyeOff } from 'lucide-react'
import toast from 'react-hot-toast'

export default function Register() {
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [showPassword, setShowPassword] = useState(false)
  const [loading, setLoading] = useState(false)
  const { register, isAuthenticated } = useAuth()
  const navigate = useNavigate()

  if (isAuthenticated) {
    navigate('/dashboard', { replace: true })
    return null
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault()
    if (!name || !email || !password) {
      toast.error('Preencha todos os campos')
      return
    }
    if (password.length < 6) {
      toast.error('A senha deve ter no mínimo 6 caracteres')
      return
    }

    setLoading(true)
    try {
      await register(name, email, password)
      toast.success('Conta criada com sucesso!')
      navigate('/dashboard', { replace: true })
    } catch (err: any) {
      toast.error(err.response?.data?.message || 'Erro ao criar conta')
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-primary-600 to-primary-900 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-white rounded-2xl shadow-lg mb-4">
            <Building2 className="text-primary-600" size={32} />
          </div>
          <h1 className="text-3xl font-bold text-white">ERP Stocco</h1>
          <p className="text-primary-200 mt-2">Criar nova conta</p>
        </div>

        <div className="bg-white rounded-2xl shadow-xl p-8">
          <h2 className="text-xl font-semibold text-gray-900 mb-6">Cadastro</h2>

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="label">Nome</label>
              <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                className="input"
                placeholder="Seu nome"
                required
              />
            </div>

            <div>
              <label className="label">Email</label>
              <input
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                className="input"
                placeholder="seu@email.com"
                required
              />
            </div>

            <div>
              <label className="label">Senha</label>
              <div className="relative">
                <input
                  type={showPassword ? 'text' : 'password'}
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="input pr-10"
                  placeholder="Mínimo 6 caracteres"
                  required
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                >
                  {showPassword ? <EyeOff size={18} /> : <Eye size={18} />}
                </button>
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="btn-primary w-full py-3"
            >
              {loading ? (
                <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white" />
              ) : (
                <UserPlus size={18} />
              )}
              {loading ? 'Criando conta...' : 'Criar Conta'}
            </button>
          </form>

          <div className="mt-6 text-center">
            <p className="text-sm text-gray-500">
              Já tem uma conta?{' '}
              <Link to="/login" className="text-primary-600 font-medium hover:text-primary-700">
                Entrar
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  )
}
