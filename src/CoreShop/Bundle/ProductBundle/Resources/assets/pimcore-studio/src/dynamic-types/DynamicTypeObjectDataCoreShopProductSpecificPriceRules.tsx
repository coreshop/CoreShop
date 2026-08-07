/**
 * CoreShop ProductBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import {
  DynamicTypeObjectDataAbstract
} from '@pimcore/studio-ui-bundle/modules/element'
import { ProductSpecificPriceRulesPanel } from '../modules/product-specific-price-rules/components/ProductSpecificPriceRulesPanel'
import type { ProductSpecificPriceRulesData } from '../modules/product-specific-price-rules/types'

const emptyData: ProductSpecificPriceRulesData = {
  actions: [],
  conditions: [],
  rules: []
}

export class DynamicTypeObjectDataCoreShopProductSpecificPriceRules extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopProductSpecificPriceRules'

  getObjectDataComponent(props: any): React.ReactElement {
    const { noteditable, defaultFieldWidth, ...rest } = props

    return (
      <ProductSpecificPriceRulesPanel
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
