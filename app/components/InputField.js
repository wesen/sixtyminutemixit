export const InputField = ({ value, name, handleChange }) => {
  const showIcon = false

  return (
    <div className="inline-flex w-full px-4 py-2 text-gray-500 items-center">
      <div className="mx-auto w-full">
        <label htmlFor="username" className="text-sm text-gray-400">
          {name}
        </label>
        <div className="w-full inline-flex border">
          {showIcon ? (
            <div className="pt-2 w-1/12 bg-gray-100 bg-opacity-50">
              <svg
                fill="none"
                className="w-6 text-gray-400 mx-auto"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                />
              </svg>
            </div>
          ) : (
            <div></div>
          )}
          <input
            id="username"
            className="w-11/12 focus:outline-none focus:text-gray-600 p-2"
            type="text"
            value={value || ''}
            onChange={(e) => handleChange(e.target.value)}
          />
        </div>
      </div>
    </div>
  )
}
