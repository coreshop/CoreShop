/**
 * CoreShop OrderBundle Sales Module
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

// List components (Pimcore DataObject Lists)
export { OrderList } from './OrderList'
export { CartList } from './CartList'
export { QuoteList } from './QuoteList'

// Detail view components
export { SaleDetail } from './SaleDetail'
export { SaleEditorTabs } from './SaleEditorTabs'

// Standalone detail widgets (not integrated with Pimcore DataObject editor)
export { OrderDetailWidget } from './OrderDetailWidget'
export { CartDetailWidget } from './CartDetailWidget'
export { QuoteDetailWidget } from './QuoteDetailWidget'

// Legacy manager components (kept for compatibility if needed)
export { OrderManager } from './OrderManager'
export { CartManager } from './CartManager'
export { QuoteManager } from './QuoteManager'
export { SaleManager } from './SaleManager'

// Types and API
export * from './types'
export * from './api'

// Extensions
export * from './extensions'
