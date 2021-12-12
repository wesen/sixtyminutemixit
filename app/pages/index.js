import { useEffect, useState } from 'react'
import { supabase } from '../utils/supabaseClient'
import Auth from '../components/Auth'
import Account from '../components/Account'

export default function Home() {
  const [session, setSession] = useState(null)

  useEffect(() => {
    setSession(supabase.auth.session())

    supabase.auth.onAuthStateChange((_event, session) => {
      setSession(session)
    })
  }, [])

  return (
    <div className="h-screen bg-gray-50 h-screen">
      <div className="container mx-auto max-w-2xl shadow-md">
        {!session ? (
          <div className="flex flex-col justify-center items-center">
            <Auth />
          </div>
        ) : (
          <Account key={session.user.id} session={session} />
        )}
      </div>
    </div>
  )
}
