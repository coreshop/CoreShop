/**
 * CoreShop OrderBundle - Button Registry
 *
 * Registry for toolbar buttons that tabs can add dynamically
 */

import type React from 'react'

export interface ToolbarButton {
  key: string
  component: React.ComponentType
  priority?: number
}

export class ButtonRegistry {
  private buttons: Map<string, ToolbarButton> = new Map()
  private changeCallback?: () => void

  /**
   * Set callback that gets called when buttons change
   */
  setChangeCallback(callback: () => void): void {
    this.changeCallback = callback
  }

  /**
   * Add a button to the toolbar
   */
  add(key: string, component: React.ComponentType, priority: number = 100): void {
    this.buttons.set(key, { key, component, priority })
    this.changeCallback?.()
  }

  /**
   * Remove a button from the toolbar
   */
  remove(key: string): void {
    this.buttons.delete(key)
    this.changeCallback?.()
  }

  /**
   * Get all buttons sorted by priority (lower = first)
   */
  getAll(): ToolbarButton[] {
    return Array.from(this.buttons.values()).sort((a, b) =>
      (a.priority ?? 100) - (b.priority ?? 100)
    )
  }

  /**
   * Clear all buttons
   */
  clear(): void {
    this.buttons.clear()
    this.changeCallback?.()
  }
}
