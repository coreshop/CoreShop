/**
 * CoreShop Assign to Existing Company Button Component
 *
 * Menu button that opens the "Assign Customer to Existing Company" modal.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { createPortal } from 'react-dom'
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { type MenuButtonProps } from '@coreshop/menu/src'
import { AssignToExistingCompanyModal } from '../modules/customer-company-assignment'

// Global state to persist modal visibility across re-renders
let globalModalOpen = false
let globalSetModalOpen: React.Dispatch<React.SetStateAction<boolean>> | null = null

export const AssignToExistingCompanyButton = ({ icon, label }: MenuButtonProps): React.JSX.Element => {
  const { t } = useTranslation()
  const [modalOpen, setModalOpen] = React.useState(globalModalOpen)

  // Sync local state with global state
  React.useEffect(() => {
    globalSetModalOpen = setModalOpen
    setModalOpen(globalModalOpen)
    return () => {
      globalSetModalOpen = null
    }
  }, [])

  // Update global state when local state changes
  React.useEffect(() => {
    globalModalOpen = modalOpen
  }, [modalOpen])

  const handleClick = (e: React.MouseEvent): void => {
    e.preventDefault()
    e.stopPropagation()
    globalModalOpen = true
    setModalOpen(true)
    if (globalSetModalOpen) {
      globalSetModalOpen(true)
    }
  }

  const handleClose = (): void => {
    globalModalOpen = false
    setModalOpen(false)
  }

  return (
    <>
      <button
        className="main-nav__list-btn"
        onClick={handleClick}
      >
        <Icon value={icon} />
        {label || t('coreshop_customer_to_company_assign_to_existing', { defaultValue: 'Assign to Existing Company' })}
      </button>

      {createPortal(
        <AssignToExistingCompanyModal
          open={modalOpen}
          onSuccess={handleClose}
          onCancel={handleClose}
        />,
        document.body
      )}
    </>
  )
}
