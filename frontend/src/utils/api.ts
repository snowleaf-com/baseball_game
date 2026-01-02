import axios, { AxiosInstance, AxiosError } from 'axios'
import {
  AuthResponse,
  User,
  Game,
  RegisterRequest,
  LoginRequest,
  ApiError,
} from '@/types'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8080/api/v1'

class ApiClient {
  private client: AxiosInstance

  constructor() {
    this.client = axios.create({
      baseURL: API_URL,
      headers: {
        'Content-Type': 'application/json',
      },
    })

    // リクエストインターセプター: トークンを自動追加
    this.client.interceptors.request.use((config) => {
      const token = localStorage.getItem('token')
      if (token) {
        config.headers.Authorization = `Bearer ${token}`
      }
      return config
    })

    // レスポンスインターセプター: エラーハンドリング
    this.client.interceptors.response.use(
      (response) => response,
      (error: AxiosError<ApiError>) => {
        if (error.response?.status === 401) {
          localStorage.removeItem('token')
          localStorage.removeItem('user')
          window.location.href = '/login'
        }
        return Promise.reject(error)
      }
    )
  }

  // 認証
  async login(data: LoginRequest): Promise<AuthResponse> {
    const response = await this.client.post<AuthResponse>('/login', data)
    return response.data
  }

  async register(data: RegisterRequest): Promise<AuthResponse> {
    const response = await this.client.post<AuthResponse>('/register', data)
    return response.data
  }

  async logout(): Promise<void> {
    await this.client.post('/logout')
  }

  async getMe(): Promise<User> {
    const response = await this.client.get<User>('/me')
    return response.data
  }

  // 試合
  async getGames(): Promise<Game[]> {
    const response = await this.client.get<Game[]>('/games')
    return response.data
  }

  async getGame(id: number): Promise<Game> {
    const response = await this.client.get<Game>(`/games/${id}`)
    return response.data
  }

  async playGame(data: {
    opponent_team_id: number
    batting_order: number[]
    boss_type: { b_act: number; b_bnt: number; b_ste: number; b_mnd: number }
  }): Promise<Game> {
    const response = await this.client.post<Game>('/games/play', data)
    return response.data
  }
}

export const apiClient = new ApiClient()
export default apiClient
