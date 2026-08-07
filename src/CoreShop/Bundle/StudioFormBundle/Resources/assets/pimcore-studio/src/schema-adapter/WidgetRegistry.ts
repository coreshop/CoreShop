/**
 * CoreShop Schema Adapter - Widget Registry
 *
 * Maps Symfony block prefix names to React component resolvers.
 * Walks blockPrefixes from most-specific to least-specific,
 * mirroring Symfony's Twig block rendering strategy.
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
 * Registry for mapping Symfony block prefixes to React components.
 *
 * Resolution walks the field's blockPrefixes array from most-specific
 * to least-specific (right to left), exactly like Symfony's Twig rendering
 * finds template blocks.
 *
 * Example: blockPrefixes = ['form', 'text', 'email']
 * Tries: 'email' → 'text' → 'form'
 */
export class WidgetRegistry {
  private resolvers = new Map<string, WidgetResolver>()

  /**
   * Register a widget resolver for a block prefix.
   */
  register(blockPrefix: string, resolver: WidgetResolver): void {
    this.resolvers.set(blockPrefix, resolver)
  }

  /**
   * Get the resolver for a block prefix.
   */
  get(blockPrefix: string): WidgetResolver | undefined {
    return this.resolvers.get(blockPrefix)
  }

  /**
   * Check if a resolver exists for a block prefix.
   */
  has(blockPrefix: string): boolean {
    return this.resolvers.has(blockPrefix)
  }

  /**
   * Resolve a field to a React component and props.
   *
   * Walks blockPrefixes from most-specific to least-specific.
   */
  resolve(field: FormSchemaField): WidgetResolverResult | null {
    const prefixes = field.blockPrefixes
    for (let i = prefixes.length - 1; i >= 0; i--) {
      const resolver = this.resolvers.get(prefixes[i])
      if (resolver) {
        const result = resolver(field)
        if (result) {
          return result
        }
      }
    }
    return null
  }

  /**
   * Get all registered block prefixes.
   */
  getRegisteredTypes(): string[] {
    return Array.from(this.resolvers.keys())
  }
}
