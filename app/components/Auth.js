import { useState } from 'react'
import { supabase } from '../utils/supabaseClient'
import { Button } from './Button'

export default function Auth() {
  const [loading, setLoading] = useState(false)
  const [email, setEmail] = useState('')

  const handleLogin = async (email) => {
    try {
      setLoading(true)
      const { error } = await supabase.auth.signIn({ email })
      if (error) throw error
      alert('Check your email for the login link')
    } catch (error) {
      alert(error.error_description || error.message)
    } finally {
      setLoading(false)
    }
  }

  async function signInWithFacebook() {
    const { user, session, error } = await supabase.auth.signIn({
      provider: 'facebook',
    })
  }

  return (
    <div className="justify-center items-center flex flex-col">
      <style>
        {`.facebook-logo {
        background-position: -414px -259px;
        background-image: url(https://bit.ly/3v2LT17);
        height: 16px;
        width: 16px;
      }`}
      </style>
      <div className="bg-white border border-gray-300 w-80 py-8 flex items-center flex-col mb-3">
        <h1 className="text-2xl font-bold">Supabase + Next.js</h1>
        <div className="text-sm w-64 mt-2">
          Sign in via magic link with your email below
        </div>
        <form className="mt-4 w-64 flex flex-col">
          <input
            className="text-xs w-full mb-2 rounded border bg-gray-100 border-gray-300 px-2 py-2
            focus:outline-none focus:border-gray-400 active:outline-none"
            type="email"
            placeholder="Your email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
          <Button
            onClick={(e) => {
              e.preventDefault()
              handleLogin(email)
            }}
            disabled={loading}
          >
            <span>{loading ? 'Loading' : 'Send magic link'}</span>
          </Button>
        </form>
        <div className="flex justify-evenly space-x-2 w-64 mt-4">
          <span className="bg-gray-300 h-px flex-grow t-2 relative top-2"></span>
          <span className="flex-none uppercase text-xs text-gray-400 font-semibold">
            or
          </span>
          <span className="bg-gray-300 h-px flex-grow t-2 relative top-2"></span>
        </div>
        <button
          className="mt-4 flex"
          onClick={(e) => {
            e.preventDefault()
            signInWithFacebook()
          }}
        >
          <div className="bg-no-repeat facebook-logo mr-1"></div>
          <span className="text-xs text-blue-900 font-semibold">
            Log in with Facebook
          </span>
        </button>
      </div>
    </div>
  )
}
