/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { DynamicTypeObjectDataAbstract } from '@pimcore/studio-ui-bundle/modules/element'

export class DynamicTypeObjectDataCoreShopSerializedData extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopSerializedData'

  getObjectDataComponent(props: any): React.ReactElement {
    return (
      <div style={{ padding: 8, color: '#999' }}>
        Serialized data (read-only)
      </div>
    )
  }
}
