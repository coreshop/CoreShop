/**
 * CoreShop OrderBundle - Modal Field Extension Registry
 *
 * Allows other bundles to inject additional fields into modals.
 * Example: CoreBundle adds carrier selection to CreateShipmentModal
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
import { injectable } from 'inversify'

export interface ModalFieldExtension {
  (props: any): React.ReactNode
}

@injectable()
export class ModalFieldExtensionRegistry {
  private extensions: Map<string, ModalFieldExtension[]> = new Map()

  /**
   * Register a field extension for a specific modal
   * @param modalKey - The modal identifier (e.g., 'create-shipment')
   * @param extension - Function that returns React node(s) to render
   */
  register(modalKey: string, extension: ModalFieldExtension): void {
    if (!this.extensions.has(modalKey)) {
      this.extensions.set(modalKey, [])
    }
    this.extensions.get(modalKey)!.push(extension)
  }

  /**
   * Get all field extensions for a modal
   * @param modalKey - The modal identifier
   * @param props - Props to pass to each extension
   * @returns Array of React nodes to render
   */
  getFields(modalKey: string, props: any): React.ReactNode[] {
    const extensions = this.extensions.get(modalKey) || []
    return extensions.map((ext) => ext(props))
  }

  /**
   * Check if any extensions are registered for a modal
   */
  hasExtensions(modalKey: string): boolean {
    return this.extensions.has(modalKey) && this.extensions.get(modalKey)!.length > 0
  }
}
