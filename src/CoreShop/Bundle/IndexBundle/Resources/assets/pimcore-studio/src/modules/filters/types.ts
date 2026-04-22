/**
 * CoreShop IndexBundle Filter Types
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type { EntityListItem } from '@coreshop/resource/src/entities/types'

/**
 * Filter Condition Interface
 */
export interface FilterCondition {
  id?: number
  type: string
  field?: string
  label?: string
  quantityUnit?: number
  configuration?: Record<string, any>
  sort?: number
}

/**
 * Filter Interface
 */
export interface Filter extends EntityListItem {
  name: string
  resultsPerPage?: number
  orderDirection?: 'asc' | 'desc'
  orderKey?: string
  index?: number | null
  preConditions?: FilterCondition[]
  conditions?: FilterCondition[]
}

/**
 * Filter Configuration Response
 */
export interface FilterConfig {
  success: boolean
  pre_conditions: string[]
  user_conditions: string[]
  schemas?: Record<string, any>
  preConditionSchemaByType?: Record<string, string>
  userConditionSchemaByType?: Record<string, string>
}

/**
 * Index Field
 */
export interface IndexField {
  name: string
}

/**
 * Field Value for preSelect
 */
export interface FieldValue {
  key: string | number
  value: string
}

/**
 * Condition Component Props
 */
export interface ConditionProps {
  data: FilterCondition
  onChange: (data: Partial<FilterCondition>) => void
  indexId?: number
  registryId?: symbol | string
}
