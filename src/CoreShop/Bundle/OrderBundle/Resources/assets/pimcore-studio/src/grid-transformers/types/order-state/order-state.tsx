/**
 * CoreShop OrderBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { type ReactElement } from 'react'
import { DynamicTypePipelineAbstract } from '@pimcore/studio-ui-bundle/modules/element'
import { OrderStateTransformerComponent } from '../../components/order-state/order-state'

export class DynamicTypePipelineGridTransformersOrderState extends DynamicTypePipelineAbstract {
  readonly id = 'coreshop_order_state'
  readonly group = 'other'

  getComponent (): ReactElement {
    return <OrderStateTransformerComponent />
  }
}
