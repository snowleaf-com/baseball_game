import { useEffect } from 'react'
import { useNavigate } from 'react-router'
import useSWR from 'swr'
import { User } from '@/types'
import apiClient from '@/utils/api'
import { fetcher } from '@/utils/fetcher'
import './Dashboard.css'

export default function Dashboard() {
  const navigate = useNavigate()
  const token = typeof window !== 'undefined' ? localStorage.getItem('token') : null
  
  const { data: user, isLoading, error } = useSWR<User>(
    token ? '/me' : null, // トークンがない場合はフェッチしない
    fetcher
  )

  useEffect(() => {
    // トークンがない場合はログイン画面へ
    if (!token) {
      navigate('/login')
      return
    }

    // エラーが発生した場合（401など）はログイン画面へ
    if (error) {
      const axiosError = error as any
      if (axiosError?.response?.status === 401) {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        navigate('/login')
      }
    }
  }, [token, error, navigate])

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

  if (isLoading) {
    return <div>読み込み中...</div>
  }

  if (error || !user) {
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
