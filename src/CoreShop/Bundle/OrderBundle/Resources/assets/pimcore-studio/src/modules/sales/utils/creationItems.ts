/**
 * CoreShop OrderBundle - Document Creation Item Helpers
 *
 * Shipment and invoice creation both render a per-item grid from a Symfony
 * CollectionType. The entry type is extensible via Symfony form type extensions,
 * so neither the grid columns nor the submitted payload may be limited to a
 * hard-coded set of keys — the form schema is the single source of truth.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { fetchFormSchema } from '@coreshop/studio-form/src/schema-adapter/api'
import type { FormSchemaField } from '@coreshop/studio-form/src/schema-adapter/types'

/**
 * The per-item fields of a document creation form, as declared by the
 * collection's entry type (including fields added by form type extensions).
 */
export type CreationItemFields = FormSchemaField[]

/**
 * Read the per-item field definitions out of a document creation form schema.
 *
 * Returns an empty list when the schema has no items collection or the
 * collection carries no prototype, in which case callers fall back to
 * passing item values through unfiltered.
 */
export const loadCreationItemFields = async (
  blockPrefix: string,
  collectionField = 'items',
): Promise<CreationItemFields> => {
  const schema = await fetchFormSchema(blockPrefix)
  const collection = schema.fields.find((field) => field.name === collectionField)

  return collection?.prototype?.fields ?? []
}

/**
 * Build the payload for a single item row.
 *
 * Every field the entry type declares is passed through, which is what keeps
 * fields contributed by a form type extension (e.g. WarehouseBundle's
 * `stockItem`) alive from the grid to the create request.
 *
 * Disabled fields are omitted: Symfony ignores submitted values for them and
 * takes the server-side value instead, and some of them hold display-only
 * values such as a formatted price.
 */
export const buildCreationItemPayload = (
  item: Record<string, unknown>,
  fields: CreationItemFields,
): Record<string, unknown> => {
  if (fields.length === 0) {
    return { ...item }
  }

  const payload: Record<string, unknown> = {}

  for (const field of fields) {
    if (field.disabled === true) {
      continue
    }

    if (field.name in item) {
      payload[field.name] = item[field.name]
    }
  }

  return payload
}
