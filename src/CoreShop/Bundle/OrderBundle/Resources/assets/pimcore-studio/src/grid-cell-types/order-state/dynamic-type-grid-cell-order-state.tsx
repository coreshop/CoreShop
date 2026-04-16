/**
 * CoreShop OrderBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { type ReactElement } from 'react'
import {
  DynamicTypeGridCellAbstract,
  type AbstractGridCellDefinition
} from '@pimcore/studio-ui-bundle/modules/element'
import { OrderStateCell } from './order-state-cell'

export class DynamicTypeGridCellOrderState extends DynamicTypeGridCellAbstract {
  readonly id = 'coreshop-order-state'

  getGridCellComponent (props: AbstractGridCellDefinition): ReactElement<AbstractGridCellDefinition> {
    return <OrderStateCell {...props} />
  }
}
