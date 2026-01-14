/**
 * CoreShop Customer Company Assignment Provider
 *
 * Global provider that renders the assignment modals.
 * Include this component once at the app level.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { CustomerCompanyAssignmentService } from './CustomerCompanyAssignmentService'
import { AssignToNewCompanyModal } from './AssignToNewCompanyModal'
import { AssignToExistingCompanyModal } from './AssignToExistingCompanyModal'

interface ModalState {
  type: 'new-company' | 'existing-company'
  customerId?: number
}

export const CustomerCompanyAssignmentProvider: React.FC = () => {
  const [modalState, setModalState] = React.useState<ModalState | null>(
    CustomerCompanyAssignmentService.getState()
  )

  React.useEffect(() => {
    return CustomerCompanyAssignmentService.subscribe(setModalState)
  }, [])

  const handleClose = React.useCallback(() => {
    CustomerCompanyAssignmentService.closeModal()
  }, [])

  const handleSuccess = React.useCallback(() => {
    // Modal will close itself after success message
  }, [])

  return (
    <>
      <AssignToNewCompanyModal
        open={modalState?.type === 'new-company'}
        customerId={modalState?.customerId}
        onSuccess={handleSuccess}
        onCancel={handleClose}
      />
      <AssignToExistingCompanyModal
        open={modalState?.type === 'existing-company'}
        customerId={modalState?.customerId}
        onSuccess={handleSuccess}
        onCancel={handleClose}
      />
    </>
  )
}
