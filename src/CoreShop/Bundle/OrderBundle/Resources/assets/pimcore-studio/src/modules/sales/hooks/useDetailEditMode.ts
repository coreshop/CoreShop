/**
 * CoreShop OrderBundle - useDetailEditMode Hook
 *
 * Custom hook for managing detail edit mode state
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
import { detailEvents } from '../events/DetailEvents'

/**
 * Hook to track detail edit mode state
 *
 * This hook subscribes to edit mode changes and triggers re-renders
 * when the edit mode state changes.
 */
export function useDetailEditMode(): boolean {
  const [editMode, setEditMode] = React.useState(() => detailEvents.isEditModeActive())

  React.useEffect(() => {
    const handleUpdate = () => {
      setEditMode(detailEvents.isEditModeActive())
    }

    detailEvents.onToolbarUpdate(handleUpdate)

    return () => {
      detailEvents.offToolbarUpdate(handleUpdate)
    }
  }, [])

  return editMode
}
