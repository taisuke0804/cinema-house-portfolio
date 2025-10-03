
export type Screening = {
  id: number
  start_time: string
  end_time: string
  movie: { id: number; title: string }
}