/**
 * CoreShop ProductQuantityPriceRulesBundle Studio Plugin
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
  DynamicTypeObjectDataAbstract
} from '@pimcore/studio-ui-bundle/modules/element'
import { ProductQuantityPriceRulesPanel } from '../modules/quantity-price-rules/components/ProductQuantityPriceRulesPanel'
import type { QuantityPriceRulesFieldData } from '../modules/quantity-price-rules/types'

const emptyData: QuantityPriceRulesFieldData = {
  actions: [],
  conditions: [],
  rules: [],
  stores: {
    calculationBehaviourTypes: [],
    pricingBehaviourTypes: []
  }
}

export class DynamicTypeObjectDataCoreShopProductQuantityPriceRules extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopProductQuantityPriceRules'

  getObjectDataComponent(props: any): React.ReactElement {
    const { noteditable, defaultFieldWidth, ...rest } = props

    return (
      <ProductQuantityPriceRulesPanel
        value={rest.value ?? emptyData}
        onChange={rest.onChange}
        disabled={noteditable === true}
        currentLocale={rest.currentLocale}
        locales={rest.locales}
      />
    )
  }

  getVersionObjectDataComponent(props: any): React.ReactElement {
    return this.getObjectDataComponent({ ...props, noteditable: true })
  }
}
