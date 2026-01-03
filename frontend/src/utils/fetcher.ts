import apiClient from './api'

// useSWR用のfetcher関数
export const fetcher = async <T>(key: string): Promise<T> => {
  // keyに応じて適切なAPIメソッドを呼び出す
  if (key === '/me') {
    return apiClient.getMe() as Promise<T>
  }
  if (key === '/games') {
    return apiClient.getGames() as Promise<T>
  }
  if (key.startsWith('/games/')) {
    const id = parseInt(key.split('/')[2])
    return apiClient.getGame(id) as Promise<T>
  }
  
  throw new Error(`Unknown key: ${key}`)
}

