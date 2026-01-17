/**
 * CoreShop ProductBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createProductSpecificPriceRuleFormBuilder } from './ProductSpecificPriceRuleFormBuilder'

export const ProductSpecificPriceRuleFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createProductSpecificPriceRuleFormBuilder()
    container.bind('CoreShop/Product/ProductSpecificPriceRule/FormBuilder').toConstantValue(builder)
  }
}
