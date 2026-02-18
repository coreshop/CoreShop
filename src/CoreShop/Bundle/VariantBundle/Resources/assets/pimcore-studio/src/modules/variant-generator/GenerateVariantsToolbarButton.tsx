/**
 * CoreShop VariantBundle - Generate Variants Toolbar Button
 *
 * Renders a "Generate Variants" button in the data object editor toolbar.
 * Only visible for objects whose class is in the variant_aware stack and whose type is 'object'.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useContext } from 'react'
import { Button } from 'antd'
import { DataObjectContext, useDataObjectDraft } from '@pimcore/studio-ui-bundle/modules/data-object'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { VariantGeneratorModal } from './VariantGeneratorModal'

// Module-level cache for variant-aware classes
let cachedVariantAwareClasses: string[] | null = null
let loadPromise: Promise<string[]> | null = null

const loadVariantAwareClasses = async (): Promise<string[]> => {
  if (cachedVariantAwareClasses) return cachedVariantAwareClasses
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
      const classes = await configProvider.getAllowedClasses('coreshop.variant_aware')
      cachedVariantAwareClasses = classes
      return classes
    } catch {
      return []
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const GenerateVariantsToolbarButton: React.FC = () => {
  const { t } = useTranslation()
  const { id } = useContext(DataObjectContext)
  const { dataObject } = useDataObjectDraft(id)

  const [isVariantAware, setIsVariantAware] = React.useState(false)
  const [modalOpen, setModalOpen] = React.useState(false)

  const className = dataObject?.className
  const objectType = dataObject?.type

  React.useEffect(() => {
    if (!className) {
      setIsVariantAware(false)
      return
    }

    void (async () => {
      const classes = await loadVariantAwareClasses()
      setIsVariantAware(classes.includes(className))
    })()
  }, [className])

  // Only show for "object" type (not variants or folders) that are variant_aware
  if (!id || !isVariantAware || objectType !== 'object') {
    return null
  }

  return (
    <>
      <Button
        type="default"
        onClick={() => setModalOpen(true)}
      >
        {t('coreshop.variant_generator.generate', { defaultValue: 'Generate Variants' })}
      </Button>
      {modalOpen && (
        <VariantGeneratorModal
          open={modalOpen}
          objectId={id}
          onClose={() => setModalOpen(false)}
        />
      )}
    </>
  )
}