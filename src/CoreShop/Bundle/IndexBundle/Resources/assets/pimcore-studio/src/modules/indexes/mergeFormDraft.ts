/**
 * Merge nested schema form drafts without losing previously edited values.
 * Ant Design's onValuesChange only returns changed fragments.
 */
const isPlainObject = (value: unknown): value is Record<string, any> => {
  return typeof value === 'object' && value !== null && !Array.isArray(value)
}

export const mergeFormDraft = (
  current: Record<string, any>,
  draft: Record<string, any>,
): Record<string, any> => {
  const result: Record<string, any> = { ...current }

  for (const [key, value] of Object.entries(draft)) {
    const previous = result[key]

    if (isPlainObject(previous) && isPlainObject(value)) {
      result[key] = mergeFormDraft(previous, value)
    } else {
      result[key] = value
    }
  }

  return result
}
