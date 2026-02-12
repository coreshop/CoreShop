/**
 * CoreShop Schema Adapter - Widget Registry
 *
 * Maps widget type strings to React component resolvers.
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
import type { FormSchemaField } from './types'
import type { FieldDefinition } from '../form-builder/types'

/**
 * Result of resolving a widget
 */
export interface WidgetResolverResult {
  /** React component to render */
  component: React.ComponentType<any>
  /** Additional props for the component */
  props?: Record<string, any>
  /** Value prop name (e.g., 'checked' for Switch) */
  valuePropName?: string
  /** Extra field definition properties */
  extra?: Partial<FieldDefinition<any>>
}

/**
 * Widget resolver function
 */
export type WidgetResolver = (field: FormSchemaField) => WidgetResolverResult | null

/**
 * Registry for mapping widget type strings to React components.
 *
 * StudioFormBundle registers default widgets (input, textarea, etc.).
 * CoreShop bundles register entity-specific widgets (coreshop.zone, etc.).
 */
export class WidgetRegistry {
  private resolvers = new Map<string, WidgetResolver>()

  /**
   * Register a widget resolver for a widget type.
   */
  register(widgetType: string, resolver: WidgetResolver): void {
    this.resolvers.set(widgetType, resolver)
  }

  /**
   * Get the resolver for a widget type.
   */
  get(widgetType: string): WidgetResolver | undefined {
    return this.resolvers.get(widgetType)
  }

  /**
   * Check if a resolver exists for a widget type.
   */
  has(widgetType: string): boolean {
    return this.resolvers.has(widgetType)
  }

  /**
   * Resolve a field to a React component and props.
   */
  resolve(field: FormSchemaField): WidgetResolverResult | null {
    const resolver = this.resolvers.get(field.uiType.widget)
    if (!resolver) {
      return null
    }
    return resolver(field)
  }

  /**
   * Get all registered widget types.
   */
  getRegisteredTypes(): string[] {
    return Array.from(this.resolvers.keys())
  }
}
