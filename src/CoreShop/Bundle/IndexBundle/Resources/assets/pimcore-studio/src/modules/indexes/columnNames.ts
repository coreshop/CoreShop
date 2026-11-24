/**
 * CoreShop IndexBundle column name helpers
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { IndexColumn } from './api'

/**
 * A column is identified by its name, not by its class field: the name is the column
 * name of the index table, while several columns may read the same field (objectKey).
 * MySQL column names are case-insensitive, so the comparison is too.
 */
const normalize = (name: string): string => name.trim().toLowerCase()

/**
 * Whether `name` is used by a column other than the one at `exceptIndex`.
 */
export const isColumnNameTaken = (
  name: string,
  columns: IndexColumn[],
  exceptIndex: number | null = null
): boolean => {
  const normalized = normalize(name)

  return columns.some((column, index) =>
    index !== exceptIndex && normalize(column.name ?? '') === normalized
  )
}

/**
 * Returns `base` if it is free, otherwise the first free `base_2`, `base_3`, …
 */
export const getUniqueColumnName = (base: string, columns: IndexColumn[]): string => {
  if (base === '' || !isColumnNameTaken(base, columns)) {
    return base
  }

  let suffix = 2
  while (isColumnNameTaken(`${base}_${suffix}`, columns)) {
    suffix++
  }

  return `${base}_${suffix}`
}
