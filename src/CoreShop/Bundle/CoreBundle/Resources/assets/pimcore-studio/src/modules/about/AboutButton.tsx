/**
 * CoreShop About Modal Opener
 *
 * Opens the About CoreShop modal via a custom event listener.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { createRoot } from 'react-dom/client'
import { AboutModal } from './AboutModal'

// Container for the modal
let modalContainer: HTMLDivElement | null = null
let modalRoot: ReturnType<typeof createRoot> | null = null

export const openAboutModal = (): void => {
  // Create container if it doesn't exist
  if (!modalContainer) {
    modalContainer = document.createElement('div')
    modalContainer.id = 'coreshop-about-modal-container'
    document.body.appendChild(modalContainer)
    modalRoot = createRoot(modalContainer)
  }

  const closeModal = (): void => {
    if (modalRoot) {
      modalRoot.render(
        <AboutModal
          onCancel={closeModal}
          onClose={closeModal}
          open={false}
        />
      )
    }
  }

  // Render modal
  if (modalRoot) {
    modalRoot.render(
      <AboutModal
        onCancel={closeModal}
        onClose={closeModal}
        open={true}
      />
    )
  }
}
