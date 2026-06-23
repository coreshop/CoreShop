/**
 * CoreShop OrderBundle Detail Events
 *
 * Event system for order detail editing (preview, save, cancel)
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

type DetailEventHandler = () => void

class DetailEventEmitter {
  private readonly listeners: Map<string, DetailEventHandler[]> = new Map()
  private editModeActive: boolean = false
  private toolbarUpdateHandlers: Array<() => void> = []

  on(event: string, handler: DetailEventHandler): void {
    if (!this.listeners.has(event)) {
      this.listeners.set(event, [])
    }
    this.listeners.get(event)!.push(handler)
  }

  off(event: string, handler: DetailEventHandler): void {
    const handlers = this.listeners.get(event)
    if (handlers) {
      const index = handlers.indexOf(handler)
      if (index > -1) {
        handlers.splice(index, 1)
      }
    }
  }

  emit(event: string): void {
    // Track edit mode state
    const wasEditMode = this.editModeActive

    if (event === 'edit') {
      this.editModeActive = true
    } else if (event === 'save' || event === 'cancel') {
      this.editModeActive = false
    }

    const handlers = this.listeners.get(event)
    if (handlers) {
      handlers.forEach(handler => handler())
    }

    // Notify toolbar to update if edit mode changed
    if (wasEditMode !== this.editModeActive) {
      this.notifyToolbarUpdate()
    }
  }

  isEditModeActive(): boolean {
    return this.editModeActive
  }

  onToolbarUpdate(handler: () => void): void {
    this.toolbarUpdateHandlers.push(handler)
  }

  offToolbarUpdate(handler: () => void): void {
    const index = this.toolbarUpdateHandlers.indexOf(handler)
    if (index > -1) {
      this.toolbarUpdateHandlers.splice(index, 1)
    }
  }

  private notifyToolbarUpdate(): void {
    this.toolbarUpdateHandlers.forEach(handler => handler())
  }
}

export const detailEvents = new DetailEventEmitter()

export const DETAIL_EVENTS = {
  EDIT: 'edit',
  PREVIEW: 'preview',
  SAVE: 'save',
  CANCEL: 'cancel'
}
