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
 * UI type descriptor from backend
 */
export interface FormSchemaUiType {
  widget: string
  /** Entity type for entitySelect widget */
  entityType?: string
  /** Whether multiple selection is allowed */
  multiple?: boolean
  /** Choices for select widget */
  choices?: Array<{ value: string | number; label: string }>
  /** Whether collection allows adding items */
  allowAdd?: boolean
  /** Whether collection allows deleting items */
  allowDelete?: boolean
  /** Any additional options */
  [key: string]: any
}

/**
 * Field schema from backend
 */
export interface FormSchemaField {
  name: string
  blockPrefix: string
  required: boolean
  uiType: FormSchemaUiType
  /** Children for compound fields (like translations) */
  children?: FormSchemaResponse
  /** Tab assignment (from enricher) */
  tab?: string
  /** Section assignment (from enricher) */
  section?: string
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
