/**
 * CoreShop Schema Adapter - Types
 *
 * JSON Schema types matching the backend FormSchemaGenerator output.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

/**
 * Field schema from backend (serialized from Symfony FormView)
 */
export interface FormSchemaField {
  name: string
  /** Symfony block prefix chain, e.g. ['form', 'text', 'email'] */
  blockPrefixes: string[]
  required: boolean
  label?: string | null
  disabled?: boolean
  /** Choices for choice-based fields */
  choices?: Array<{ value: string | number; label: string; group?: string }>
  /** Whether multiple selection is allowed (choice fields) */
  multiple?: boolean
  /** Whether choices are rendered as expanded (radio/checkbox) */
  expanded?: boolean
  /** Extra vars from FormView (e.g. relation_class, allow_add) */
  extra?: Record<string, any>
  /** Children for compound fields (like translations) */
  children?: FormSchemaResponse
  /** Single prototype (standard Symfony CollectionType) */
  prototype?: FormSchemaResponse
  /** Multiple prototypes keyed by type (CoreShop condition/action collections) */
  prototypes?: Record<string, FormSchemaResponse>
  /** Tab assignment (from enricher) */
  tab?: string | null
  /** Section assignment (from enricher) */
  section?: string | null
}

/**
 * Tab schema from backend enricher
 */
export interface FormSchemaTab {
  key: string
  label: string
  order: number
  /** Custom widget for this tab (e.g., 'shippingRuleManager') */
  widget?: string
}

/**
 * Section schema from backend enricher
 */
export interface FormSchemaSection {
  key: string
  label: string
  order: number
  collapsible: boolean
  defaultCollapsed: boolean
}

/**
 * Complete form schema response from backend
 */
export interface FormSchemaResponse {
  blockPrefix: string
  fields: FormSchemaField[]
  tabs: FormSchemaTab[]
  sections: FormSchemaSection[]
}
