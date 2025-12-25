import React, { useState, useEffect } from 'react'
import { Link } from 'react-router'
import { Game, Comment, LeagueInfo } from '@/types'
import apiClient from '@/utils/api'
import './Home.css'

export default function Home() {
  const [recentGames, setRecentGames] = useState<Game[]>([]);
  const [comments, setComments] = useState<Comment[]>([]);
  const [leagueInfo, setLeagueInfo] = useState<LeagueInfo | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        const games = await apiClient.getGames();
        setRecentGames(games);
        // TODO: コメントとリーグ情報を取得
        setLoading(false);
      } catch (error) {
        console.error('Error fetching data:', error);
        setLoading(false);
      }
    }

    fetchData()
  }, [])

  return (
    <div className="home">
      <header className="home-header">
        <h1>激パワプロ野球リーグ</h1>
        <p>野球リーグ戦シミュレーションゲーム</p>
      </header>

      <nav className="home-nav">
        <Link to="/login" className="btn btn-primary">ログイン</Link>
        <Link to="/register" className="btn btn-secondary">新規登録</Link>
      </nav>

      <main className="home-main">
        <section className="league-info">
          <h2>リーグ情報</h2>
          {loading ? (
            <p>リーグ情報を読み込み中...</p>
          ) : leagueInfo ? (
            <div>
              <p>第{leagueInfo.league_number}回リーグ</p>
              <p>リーグ{leagueInfo.league_day}日目</p>
            </div>
          ) : (
            <p>リーグ情報がありません</p>
          )}
        </section>

        <section className="recent-games">
          <h2>最近の試合</h2>
          {loading ? (
            <p>読み込み中...</p>
          ) : recentGames.length > 0 ? (
            <ul>
              {recentGames.map((game) => (
                <li key={game.id}>
                  {game.home_team?.team_name} {game.home_score} - {game.away_score} {game.away_team?.team_name}
                </li>
              ))}
            </ul>
          ) : (
            <p>試合がありません</p>
          )}
        </section>

        <section className="comments">
          <h2>コメント</h2>
          {comments.length > 0 ? (
            <ul>
              {comments.map((comment) => (
                <li key={comment.id}>
                  <strong>{comment.saku || 'システム'}:</strong> {comment.comment}
                </li>
              ))}
            </ul>
          ) : (
            <p>コメントがありません</p>
          )}
        </section>
      </main>
    </div>
  )
}

