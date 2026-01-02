import React, { useState } from 'react'
import { useNavigate } from 'react-router'
import { RegisterRequest, PlayerFormData, BossType } from '@/types'
import apiClient from '@/utils/api'
import './Register.css'

const POSITIONS = ['捕', '一', '二', '三', '遊', '左', '中', '右']

export default function Register() {
  const navigate = useNavigate()
  const [formData, setFormData] = useState<RegisterRequest>({
    saku: '',
    password: '',
    team_name: '',
    home_url: '',
    icon: '',
    boss_type: {
      b_act: 5,
      b_bnt: 5,
      b_ste: 5,
      b_mnd: 5,
    },
    players: Array(10)
      .fill(null)
      .map(
        (_, i): PlayerFormData => ({
          name: '',
          position: i < 8 ? POSITIONS[i] : undefined,
          power: 5,
          meet: 5,
          run: 5,
          defense: 5,
        })
      ),
  })
  const [error, setError] = useState<string>('')
  const [loading, setLoading] = useState<boolean>(false)

  const handlePlayerChange = (
    index: number,
    field: keyof PlayerFormData,
    value: string | number | undefined
  ): void => {
    const newPlayers = [...formData.players]
    newPlayers[index] = { ...newPlayers[index], [field]: value }
    setFormData({ ...formData, players: newPlayers })
  }

  const handleBossTypeChange = (field: keyof BossType, value: number): void => {
    setFormData({
      ...formData,
      boss_type: { ...formData.boss_type, [field]: value },
    })
  }

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>): Promise<void> => {
    e.preventDefault()
    setError('')
    setLoading(true)

    try {
      const response = await apiClient.register(formData)
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
          <label>ホームページURL</label>
          <input
            type="url"
            value={formData.home_url}
            onChange={(e) => setFormData({ ...formData, home_url: e.target.value })}
            disabled={loading}
          />
        </div>

        <h2>ボスタイプ</h2>
        <div className="boss-type">
          <div>
            <label>打撃I</label>
            <input
              type="number"
              min="1"
              max="10"
              value={formData.boss_type.b_act}
              onChange={(e) => handleBossTypeChange('b_act', parseInt(e.target.value))}
              disabled={loading}
            />
          </div>
          <div>
            <label>バントI</label>
            <input
              type="number"
              min="1"
              max="10"
              value={formData.boss_type.b_bnt}
              onChange={(e) => handleBossTypeChange('b_bnt', parseInt(e.target.value))}
              disabled={loading}
            />
          </div>
          <div>
            <label>走塁I</label>
            <input
              type="number"
              min="1"
              max="10"
              value={formData.boss_type.b_ste}
              onChange={(e) => handleBossTypeChange('b_ste', parseInt(e.target.value))}
              disabled={loading}
            />
          </div>
          <div>
            <label>守備I</label>
            <input
              type="number"
              min="1"
              max="10"
              value={formData.boss_type.b_mnd}
              onChange={(e) => handleBossTypeChange('b_mnd', parseInt(e.target.value))}
              disabled={loading}
            />
          </div>
        </div>

        <h2>選手登録</h2>
        {formData.players.map((player, index) => (
          <div key={index} className="player-form">
            <h3>選手 {index + 1}</h3>
            <div>
              <label>名前</label>
              <input
                type="text"
                value={player.name}
                onChange={(e) => handlePlayerChange(index, 'name', e.target.value)}
                required
                disabled={loading}
              />
            </div>
            {index < 8 && (
              <div>
                <label>守備位置</label>
                <select
                  value={player.position || ''}
                  onChange={(e) => handlePlayerChange(index, 'position', e.target.value)}
                  disabled={loading}
                >
                  {POSITIONS.map((pos) => (
                    <option key={pos} value={pos}>
                      {pos}
                    </option>
                  ))}
                </select>
              </div>
            )}
            <div>
              <label>パワー</label>
              <input
                type="number"
                min="1"
                max="10"
                value={player.power}
                onChange={(e) => handlePlayerChange(index, 'power', parseInt(e.target.value))}
                required
                disabled={loading}
              />
            </div>
            <div>
              <label>ミート</label>
              <input
                type="number"
                min="1"
                max="10"
                value={player.meet}
                onChange={(e) => handlePlayerChange(index, 'meet', parseInt(e.target.value))}
                required
                disabled={loading}
              />
            </div>
            <div>
              <label>走力</label>
              <input
                type="number"
                min="1"
                max="10"
                value={player.run}
                onChange={(e) => handlePlayerChange(index, 'run', parseInt(e.target.value))}
                required
                disabled={loading}
              />
            </div>
            <div>
              <label>守備</label>
              <input
                type="number"
                min="1"
                max="10"
                value={player.defense}
                onChange={(e) => handlePlayerChange(index, 'defense', parseInt(e.target.value))}
                required
                disabled={loading}
              />
            </div>
          </div>
        ))}

        <button type="submit" disabled={loading}>
          {loading ? '登録中...' : '登録'}
        </button>
      </form>
    </div>
  )
}
