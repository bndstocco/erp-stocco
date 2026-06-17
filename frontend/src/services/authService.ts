import api from './api'
import { User, ApiResponse } from '../types'

interface LoginResponse {
  token: string
  user: User
}

export const authService = {
  async login(email: string, password: string): Promise<LoginResponse> {
    const { data } = await api.post<ApiResponse<LoginResponse>>('/auth/login', { email, password })
    if (data.error) throw new Error(data.message)
    return data.data
  },

  async me(): Promise<User> {
    const { data } = await api.get<ApiResponse<User>>('/auth/me')
    if (data.error) throw new Error(data.message)
    return data.data
  },

  setToken(token: string) {
    localStorage.setItem('@erp_stocco:token', token)
  },

  getToken(): string | null {
    return localStorage.getItem('@erp_stocco:token')
  },

  setUser(user: User) {
    localStorage.setItem('@erp_stocco:user', JSON.stringify(user))
  },

  getUser(): User | null {
    const user = localStorage.getItem('@erp_stocco:user')
    return user ? JSON.parse(user) : null
  },

  logout() {
    localStorage.removeItem('@erp_stocco:token')
    localStorage.removeItem('@erp_stocco:user')
    window.location.href = '/login'
  },

  isAuthenticated(): boolean {
    return !!this.getToken()
  },
}
