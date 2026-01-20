/**
 * CoreShop CoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

// CoreBundle provides shared conditions and actions that get registered
// into other bundles' registries. Import them from their specific paths:
//
// import { QuantityCondition } from '@coreshop/core/product-price-rules/conditions'
// import { NotDiscountableCustomAttributesAction } from '@coreshop/core/product-price-rules/actions'

export * from './modules/product-price-rules/conditions'
export * from './modules/product-price-rules/actions'
export * from './modules/cart-price-rules/conditions'
export * from './modules/cart-price-rules/actions'
