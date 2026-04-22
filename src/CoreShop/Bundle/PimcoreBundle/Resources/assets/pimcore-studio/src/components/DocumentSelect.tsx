/**
 * CoreShop PimcoreBundle Studio Plugin
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
import {
  ManyToOneRelation,
  type ManyToOneRelationValueType,
} from '@pimcore/studio-ui-bundle/modules/element'
import { loadElementDetails } from '../api/helperApi'

interface DocumentSelectProps {
  value?: number | null
  onChange?: (value: number | null) => void
  documentTypes?: string[]
  disabled?: boolean
}

export const DocumentSelect: React.FC<DocumentSelectProps> = ({
  value,
  onChange,
  documentTypes,
  disabled,
}) => {
  const [fullPath, setFullPath] = React.useState<string | undefined>(undefined)

  React.useEffect(() => {
    if (!value) {
      setFullPath(undefined)
      return
    }

    loadElementDetails([String(value)], 'document')
      .then((details) => {
        const detail = details[String(value)]
        if (detail?.fullPath) {
          setFullPath(detail.fullPath)
        }
      })
      .catch(() => {
        setFullPath(undefined)
      })
  }, [value])

  const relationValue: ManyToOneRelationValueType = React.useMemo(() => {
    if (!value) return null
    return { type: 'document', id: value, fullPath }
  }, [value, fullPath])

  const handleChange = React.useCallback((newValue: ManyToOneRelationValueType) => {
    if (!newValue || (newValue as any).textInput) {
      onChange?.(null)
      setFullPath(undefined)
    } else {
      const rel = newValue as { id: number; fullPath?: string }
      onChange?.(rel.id)
      if (rel.fullPath) {
        setFullPath(rel.fullPath)
      }
    }
  }, [onChange])

  return (
    <ManyToOneRelation
      value={relationValue}
      onChange={handleChange}
      assetsAllowed={false}
      documentsAllowed
      allowedDocumentTypes={documentTypes}
      dataObjectsAllowed={false}
      allowToClearRelation
      disabled={disabled}
    />
  )
}
