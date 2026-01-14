/**
 * CoreShop About Button Component
 *
 * Menu button that opens the About CoreShop modal.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { createRoot } from 'react-dom/client'
import { Icon } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { type MenuButtonProps } from '@coreshop/menu/src'
import { AboutModal } from './AboutModal'

// Container for the modal
let modalContainer: HTMLDivElement | null = null
let modalRoot: ReturnType<typeof createRoot> | null = null

const openAboutModal = (): void => {
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

export const AboutButton = ({ icon, closeMainNav }: MenuButtonProps): React.JSX.Element => {
  const { t } = useTranslation()

  const handleClick = (): void => {
    closeMainNav?.()

    // Small delay to ensure menu animation completes
    setTimeout(() => {
      openAboutModal()
    }, 50)
  }

  return (
    <button
      className="main-nav__list-btn"
      onClick={handleClick}
    >
      <Icon value={icon ?? 'coreshop_nav_icon_logo'} />
      {t('coreshop_about', { defaultValue: 'About' })}
    </button>
  )
}
