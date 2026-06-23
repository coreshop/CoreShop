/**
 * CoreShop CoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { type ReactElement } from 'react'
import { DynamicTypePipelineAbstract } from '@pimcore/studio-ui-bundle/modules/element'
import { StoreValuesFieldTransformerComponent } from '../../components/store-values-field/store-values-field'

export class DynamicTypePipelineGridTransformersStoreValuesField extends DynamicTypePipelineAbstract {
  readonly id = 'coreshop_store_values_field'
  readonly group = 'other'

  getComponent (): ReactElement {
    return <StoreValuesFieldTransformerComponent />
  }
}
