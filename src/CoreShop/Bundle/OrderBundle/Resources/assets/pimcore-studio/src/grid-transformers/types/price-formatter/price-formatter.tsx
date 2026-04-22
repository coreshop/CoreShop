/**
 * CoreShop OrderBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { type ReactElement } from 'react'
import { DynamicTypePipelineAbstract } from '@pimcore/studio-ui-bundle/modules/element'
import { PriceFormatterTransformerComponent } from '../../components/price-formatter/price-formatter'

export class DynamicTypePipelineGridTransformersPriceFormatter extends DynamicTypePipelineAbstract {
  readonly id = 'coreshop_price_formatter'
  readonly group = 'other'

  getComponent (): ReactElement {
    return <PriceFormatterTransformerComponent />
  }
}
