/**
 * CoreShop Order By Number Hook
 * 
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { useState } from 'react'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'

export const useOrderByNumber = () => {
  const [modalVisible, setModalVisible] = useState(false)
  const { openDataObject } = useDataObjectHelper()

  const showModal = () => {
    setModalVisible(true)
  }

  const hideModal = () => {
    setModalVisible(false)
  }

  const handleOrderFound = (orderId: number) => {
    void openDataObject({
      config: {
        id: orderId
      }
    })
    hideModal()
  }

  return {
    modalVisible,
    showModal,
    hideModal,
    handleOrderFound,
  }
}