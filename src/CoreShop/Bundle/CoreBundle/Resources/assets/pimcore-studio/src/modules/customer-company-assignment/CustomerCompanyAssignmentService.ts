/**
 * CoreShop Customer Company Assignment Service
 *
 * Global service for opening customer-company assignment modals from anywhere.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

type ModalType = 'new-company' | 'existing-company'

interface ModalState {
  type: ModalType
  customerId?: number
}

type ModalChangeCallback = (state: ModalState | null) => void

class CustomerCompanyAssignmentServiceClass {
  private currentModal: ModalState | null = null
  private changeCallbacks: Set<ModalChangeCallback> = new Set()

  /**
   * Subscribe to modal state changes
   */
  subscribe(callback: ModalChangeCallback): () => void {
    this.changeCallbacks.add(callback)
    return () => {
      this.changeCallbacks.delete(callback)
    }
  }

  /**
   * Get current modal state
   */
  getState(): ModalState | null {
    return this.currentModal
  }

  /**
   * Open the "Assign to New Company" modal
   */
  openNewCompanyModal(customerId?: number): void {
    this.currentModal = { type: 'new-company', customerId }
    this.notifyChange()
  }

  /**
   * Open the "Assign to Existing Company" modal
   */
  openExistingCompanyModal(customerId?: number): void {
    this.currentModal = { type: 'existing-company', customerId }
    this.notifyChange()
  }

  /**
   * Close any open modal
   */
  closeModal(): void {
    this.currentModal = null
    this.notifyChange()
  }

  private notifyChange(): void {
    this.changeCallbacks.forEach(callback => callback(this.currentModal))
  }
}

// Singleton instance
export const CustomerCompanyAssignmentService = new CustomerCompanyAssignmentServiceClass()

// Service ID for dependency injection
export const customerCompanyAssignmentServiceId = 'CoreShop/CustomerCompanyAssignmentService'
