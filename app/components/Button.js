export const Button = ({ onClick, children, disabled }) => {
  return disabled ? (
    <button
      onClick={onClick}
      className="text-xs text-center bg-blue-300 text-white px-2 py-2 rounded font-medium"
      disabled={disabled}
    >
      {children}
    </button>
  ) : (
    <button
      onClick={onClick}
      className="text-xs text-center bg-blue-600 text-white px-2 py-2 rounded font-medium"
      disabled={disabled}
    >
      {children}
    </button>
  )
}
