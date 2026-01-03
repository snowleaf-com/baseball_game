export interface User {
  id: number
  saku: string
  team_name: string
  home_url?: string
  icon?: string
  wins: number
  losses: number
  win_streak: number
  max_win_streak: number
  runs_scored: number
  runs_allowed: number
  total_at_bats: number
  total_hits: number
  total_home_runs: number
  total_stolen_bases: number
  total_errors: number
  boss_type: TeamStrategy // チーム方針: 'offensive' | 'defensive' | 'balanced' | 'running'
  camp_count: number
  players?: Player[]
  created_at?: string
  updated_at?: string
}

export interface Player {
  id: number
  user_id: number
  player_number: number
  batting_order: number
  position?: string
  name: string
  condition: number
  power: number
  meet: number
  run: number
  defense: number
  at_bats: number
  hits: number
  runs: number
  home_runs: number
  stolen_bases: number
  errors: number
  four_balls: number
  strikeouts: number
  ground_into_double_play: number
  player_type: number // 0: 野手, 1: 投手
  pitch_type?: string
  fastball?: number
  changeup?: number
  control?: number
  pitching_wins?: number
  pitching_losses?: number
  innings_pitched?: number
  pitching_era?: number
  strikeouts_pitched?: number
  walks_allowed?: number
  home_runs_allowed?: number
}

// チーム方針（簡略化版）
export type TeamStrategy = 'offensive' | 'defensive' | 'balanced' | 'running'

// 後方互換性のため、BossTypeは残す（非推奨）
export interface BossType {
  b_act: number
  b_bnt: number
  b_ste: number
  b_mnd: number
}

export interface Game {
  id: number
  home_team_id: number
  away_team_id: number
  home_score: number
  away_score: number
  game_log?: GameLog[]
  played_at: string
  league_day: number
  league_number: number
  home_team?: User
  away_team?: User
  created_at?: string
  updated_at?: string
}

export interface GameLog {
  inning: number
  home_score: number
  away_score: number
}

export interface Comment {
  id: number
  is_system: boolean
  user_id?: number
  saku?: string
  home_url?: string
  comment: string
  game_result?: string
  created_at: string
  updated_at: string
}

export interface LeagueInfo {
  league_number: number
  league_day: number
  league_start_date?: string
  league_limit_days: number
  league_max_games: number
}

export interface LoginRequest {
  saku: string
  password: string
}

export interface RegisterRequest {
  saku: string
  password: string
  team_name: string
  home_url?: string
  icon?: string
  boss_type?: TeamStrategy // オプショナル：省略時は'balanced'を使用
  players?: PlayerFormData[] // オプショナル：省略時は自動生成
}

export interface PlayerFormData {
  name: string
  position?: string
  power: number
  meet: number
  run: number
  defense: number
  pitch_type?: string
}

export interface AuthResponse {
  user: User
  token: string
}

export interface ApiError {
  message: string
  errors?: Record<string, string[]>
}
