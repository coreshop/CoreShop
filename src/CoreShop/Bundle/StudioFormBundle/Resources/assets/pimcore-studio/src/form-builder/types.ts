/**
 * CoreShop Form Builder - Types
 *
 * Form Builder system inspired by Pimcore Studio's ListingBuilder pattern.
 * Allows composable, decorator-based form configuration.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import type { FormInstance, Rule } from 'antd/es/form'

/**
 * Field Definition
 */
export interface FieldDefinition<T = any> {
  /** Field name (key in data object, or array for nested paths like ['translations', '__LOCALE__', 'name']) */
  name: string | string[]
  /** Display label */
  label: string
  /** React component to render */
  component: React.ComponentType<any>
  /** Section this field belongs to */
  section?: string
  /** Tab this field belongs to */
  tab?: string
  /** Field is required */
  required?: boolean
  /** Field is disabled */
  disabled?: boolean
  /** Field is hidden (not rendered) */
  hidden?: boolean
  /** Validation rules (Ant Design format) */
  rules?: Rule[]
  /** Tooltip/help text */
  tooltip?: string
  /** Custom props passed to component */
  componentProps?: Record<string, any>
  /** Value prop name for Form.Item (e.g. 'checked' for Checkbox/Switch) */
  valuePropName?: string
  /** Field width in grid (1-24, Ant Design Col span) */
  span?: number
  /** Custom wrapper component */
  wrapper?: (children: React.ReactNode) => React.ReactNode
  /** Conditional visibility based on form data */
  visible?: (data?: T) => boolean
  /** Field is localized (will append locale to label) */
  localized?: boolean
}

/**
 * Section Definition (for grouping fields)
 */
export interface SectionDefinition {
  /** Unique section key */
  key: string
  /** Display title */
  title: string
  /** Section description */
  description?: string
  /** Section is collapsible */
  collapsible?: boolean
  /** Default collapsed state */
  defaultCollapsed?: boolean
  /** Section order/priority */
  order?: number
  /** Custom icon */
  icon?: React.ReactNode
}

/**
 * Tab Definition (for grouping sections/fields)
 */
export interface TabDefinition {
  /** Unique tab key */
  key: string
  /** Display title */
  title: string
  /** Tab order/priority */
  order?: number
}

/**
 * Form Builder Configuration
 */
export interface FormBuilderConfig<T = any> {
  /** Field definitions */
  fields: FieldDefinition<T>[]
  /** Section definitions */
  sections?: SectionDefinition[]
  /** Tab definitions */
  tabs?: TabDefinition[]
  /** Form layout */
  layout?: 'vertical' | 'horizontal' | 'inline'
  /** Number of columns in grid layout */
  columns?: number
  /** Additional form props */
  formProps?: Record<string, any>
}

/**
 * Form Decorator
 *
 * A decorator is a function that transforms FormBuilderConfig.
 * Similar to Pimcore's AbstractDecorator pattern.
 */
export interface FormDecorator<T = any> {
  (config: FormBuilderConfig<T>, context?: FormDecoratorContext<T>): FormBuilderConfig<T>
}

/**
 * Context passed to decorators
 */
export interface FormDecoratorContext<T = any> {
  /** Current form data */
  data?: T
  /** Current locale (for localized forms) */
  locale?: string
  /** Available locales */
  locales?: string[]
  /** Form instance (Ant Design) */
  form?: FormInstance
  /** Additional context data */
  [key: string]: any
}

/**
 * Decorator Registration
 */
export interface DecoratorRegistration<T = any> {
  /** Decorator name (for override/removal) */
  name: string
  /** Decorator function */
  decorator: FormDecorator<T>
}
