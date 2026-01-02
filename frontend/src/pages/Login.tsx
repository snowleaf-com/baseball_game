import React, { useState } from 'react'
import { useNavigate } from 'react-router'
import { LoginRequest } from '@/types'
import apiClient from '@/utils/api'
import './Login.css'

export default function Login() {
  const navigate = useNavigate()
  const [formData, setFormData] = useState<LoginRequest>({
    saku: '',
    password: '',
  })
  const [error, setError] = useState<string>('')
  const [loading, setLoading] = useState<boolean>(false)

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      const response = await apiClient.login(formData)
      localStorage.setItem('token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))
      navigate('/dashboard')
    } catch (err) {
      if (err instanceof Error) {
        setError(err.message)
      } else {
        setError('ログインに失敗しました')
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="login">
      <h1>ログイン</h1>
      <form onSubmit={handleSubmit}>
        {error && <div className="error">{error}</div>}
        <div>
          <label>作成者名</label>
          <input
            type="text"
            value={formData.saku}
            onChange={(e) => setFormData({ ...formData, saku: e.target.value })}
            required
            disabled={loading}
          />
        </div>
        <div>
          <label>パスワード</label>
          <input
            type="password"
            value={formData.password}
            onChange={(e) => setFormData({ ...formData, password: e.target.value })}
            required
            disabled={loading}
          />
        </div>
        <button type="submit" disabled={loading}>
          {loading ? 'ログイン中...' : 'ログイン'}
        </button>
      </form>
    </div>
  )
}
