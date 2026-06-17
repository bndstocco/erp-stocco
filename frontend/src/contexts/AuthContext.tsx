import { createContext, useContext, useState, useEffect, ReactNode } from 'react'
import { User } from '../types'
import { authService } from '../services/authService'

interface AuthContextData {
  user: User | null
  isAuthenticated: boolean
  loading: boolean
  login: (email: string, password: string) => Promise<void>
  register: (name: string, email: string, password: string) => Promise<void>
  logout: () => void
}

const AuthContext = createContext<AuthContextData>({} as AuthContextData)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    const token = authService.getToken()
    if (token) {
      authService.me()
        .then((user) => setUser(user))
        .catch(() => authService.logout())
        .finally(() => setLoading(false))
    } else {
      setLoading(false)
    }
  }, [])

  async function login(email: string, password: string) {
    const response = await authService.login(email, password)
    authService.setToken(response.token)
    authService.setUser(response.user)
    setUser(response.user)
  }

  async function register(name: string, email: string, password: string) {
    const response = await authService.register(name, email, password)
    authService.setToken(response.token)
    authService.setUser(response.user)
    setUser(response.user)
  }

  function logout() {
    setUser(null)
    authService.logout()
  }

  return (
    <AuthContext.Provider
      value={{
        user,
        isAuthenticated: !!user,
        loading,
        login,
        register,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  )
}

export function useAuth() {
  return useContext(AuthContext)
}
