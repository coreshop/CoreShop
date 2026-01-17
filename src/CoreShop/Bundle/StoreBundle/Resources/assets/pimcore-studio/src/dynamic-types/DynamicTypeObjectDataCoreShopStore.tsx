/**
 * CoreShop StoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import {
  DynamicTypeObjectDataAbstractSelect,
  DynamicTypeFieldFilterMultiselect
} from '@pimcore/studio-ui-bundle/modules/element'
import { StoreSelect } from '../components/StoreSelect'

export class DynamicTypeObjectDataCoreShopStore extends DynamicTypeObjectDataAbstractSelect {
  readonly id = 'coreShopStore'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect()

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    return (
      <StoreSelect
        value={rest.value}
        onChange={rest.onChange}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
      />
    )
  }
}
