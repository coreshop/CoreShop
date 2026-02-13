/**
 * CoreShop Schema Adapter
 *
 * Exports for the Schema Adapter system.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export * from './types'
export { WidgetRegistry } from './WidgetRegistry'
export type { WidgetResolver, WidgetResolverResult } from './WidgetRegistry'
export { toFormBuilderConfig } from './FormSchemaAdapter'
export { useFormSchema } from './useFormSchema'
export type { UseFormSchemaResult } from './useFormSchema'
export { SchemaForm } from './SchemaForm'
export type { SchemaFormProps } from './SchemaForm'
export { fetchFormSchema, clearSchemaCache, preSeedSchemaCache } from './api'
