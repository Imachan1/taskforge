import api from './axios'

export const searchWorkspace = (query) =>
  api.get('/search', {
    params: {
      q: query,
    },
  })
