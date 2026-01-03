import { Link } from 'react-router'
import useSWR from 'swr'
import { Game, Comment, LeagueInfo } from '@/types'
import { fetcher } from '@/utils/fetcher'
import './Home.css'

export default function Home() {
  const { data: recentGames, isLoading, error } = useSWR<Game[]>('/games', fetcher)
  // TODO: コメントとリーグ情報を取得
  const comments: Comment[] = []
  const leagueInfo: LeagueInfo | null = null as LeagueInfo | null

  return (
    <div className="home">
      <header className="home-header">
        <h1>激パワプロ野球リーグ</h1>
        <p>野球リーグ戦シミュレーションゲーム</p>
      </header>

      <nav className="home-nav">
        <Link to="/login" className="btn btn-primary">
          ログイン
        </Link>
        <Link to="/register" className="btn btn-secondary">
          新規登録
        </Link>
      </nav>

      <main className="home-main">
        <section className="league-info">
          <h2>リーグ情報</h2>
          {leagueInfo ? (
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
          {isLoading ? (
            <p>読み込み中...</p>
          ) : error ? (
            <p>エラーが発生しました</p>
          ) : recentGames && recentGames.length > 0 ? (
            <ul>
              {recentGames.map((game) => (
                <li key={game.id}>
                  {game.home_team?.team_name} {game.home_score} - {game.away_score}{' '}
                  {game.away_team?.team_name}
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
