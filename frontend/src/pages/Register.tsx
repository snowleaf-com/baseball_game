import React, { useState } from 'react'
import { useNavigate } from 'react-router'
import { RegisterRequest, TeamStrategy } from '@/types'
import apiClient from '@/utils/api'
import './Register.css'

const TEAM_STRATEGIES: { value: TeamStrategy; label: string; description: string }[] = [
  { value: 'offensive', label: 'オフェンス重視', description: '打撃力を重視した戦略' },
  { value: 'defensive', label: '守備重視', description: '守備力を重視した戦略' },
  { value: 'balanced', label: 'バランス型', description: '攻守のバランスを重視（推奨）' },
  { value: 'running', label: '走塁重視', description: '走力・盗塁を重視した戦略' },
]

export default function Register() {
  const navigate = useNavigate()
  const [formData, setFormData] = useState<Omit<RegisterRequest, 'players' | 'home_url'>>({
    saku: '',
    password: '',
    team_name: '',
    icon: '',
    boss_type: 'balanced', // デフォルトはバランス型
  })
  const [error, setError] = useState<string>('')
  const [loading, setLoading] = useState<boolean>(false)

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      // playersパラメータは省略（バックエンドで自動生成）
      const registerData: RegisterRequest = {
        ...formData,
        // playersは省略（バックエンドで自動生成）
      }
      const response = await apiClient.register(registerData)
      localStorage.setItem('token', response.token)
      localStorage.setItem('user', JSON.stringify(response.user))
      navigate('/dashboard')
    } catch (err) {
      if (err instanceof Error) {
        setError(err.message)
      } else {
        setError('登録に失敗しました')
      }
    } finally {
      setLoading(false)
    }
  }

  return (
    <div className="register">
      <h1>新規登録</h1>
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
          <label>チーム名</label>
          <input
            type="text"
            value={formData.team_name}
            onChange={(e) => setFormData({ ...formData, team_name: e.target.value })}
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

        <div>
          <label>チーム方針</label>
          <div className="strategy-options">
            {TEAM_STRATEGIES.map((strategy) => (
              <label key={strategy.value} className="strategy-option">
                <input
                  type="radio"
                  name="boss_type"
                  value={strategy.value}
                  checked={formData.boss_type === strategy.value}
                  onChange={(e) =>
                    setFormData({ ...formData, boss_type: e.target.value as TeamStrategy })
                  }
                  disabled={loading}
                />
                <div className="strategy-content">
                  <strong>{strategy.label}</strong>
                  <span className="strategy-description">{strategy.description}</span>
                </div>
              </label>
            ))}
          </div>
        </div>

        <div className="info-box">
          <p>※ 選手は自動生成されます。登録後、ダッシュボードで確認できます。</p>
        </div>

        <button type="submit" disabled={loading}>
          {loading ? '登録中...' : '登録'}
        </button>
      </form>
    </div>
  )
}
