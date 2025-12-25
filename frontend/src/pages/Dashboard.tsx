import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router'
import { User } from '@/types'
import apiClient from '@/utils/api'
import './Dashboard.css'

export default function Dashboard() {
  const navigate = useNavigate();
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    const fetchUser = async (): Promise<void> => {
      const token = localStorage.getItem('token')
      if (!token) {
        navigate('/login')
        return
      }

      try {
        const userData = await apiClient.getMe()
        setUser(userData)
      } catch (error) {
        console.error('Error fetching user:', error)
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        navigate('/login')
      } finally {
        setLoading(false)
      }
    }

    fetchUser()
  }, [navigate])

  const handleLogout = async (): Promise<void> => {
    try {
      await apiClient.logout()
    } catch (error) {
      console.error('Error logging out:', error)
    } finally {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      navigate('/')
    }
  }

  if (loading) {
    return <div>読み込み中...</div>
  }

  if (!user) {
    return <div>ユーザー情報の取得に失敗しました</div>
  }

  const games = user.wins + user.losses
  const winRate = games > 0 ? ((user.wins / games) * 1000).toFixed(3) : '0.000'

  return (
    <div className="dashboard">
      <h1>{user.team_name} ダッシュボード</h1>
      <div className="stats">
        <div>試合数: {games}</div>
        <div>勝利: {user.wins}</div>
        <div>敗北: {user.losses}</div>
        <div>勝率: {winRate}</div>
        <div>連勝: {user.win_streak}</div>
        <div>最大連勝: {user.max_win_streak}</div>
      </div>
      <div className="actions">
        <button onClick={() => navigate('/game')}>試合を開始</button>
        <button onClick={() => navigate('/ranking')}>ランキング</button>
        <button onClick={handleLogout}>ログアウト</button>
      </div>
    </div>
  )
}

