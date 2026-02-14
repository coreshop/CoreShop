/**
 * Build a user-facing label for rule condition/action types.
 * Examples:
 * - "mail" -> "Action: mail"
 * - "user.mail" -> "Action: mail (user)"
 */
export const formatTypeLabel = (kind: 'Action' | 'Condition', type: string): string => {
  const dotIndex = type.indexOf('.')
  if (dotIndex <= 0 || dotIndex >= type.length - 1) {
    return `${kind}: ${type}`
  }

  const scope = type.slice(0, dotIndex)
  const name = type.slice(dotIndex + 1)

  return `${kind}: ${name} (${scope})`
}

