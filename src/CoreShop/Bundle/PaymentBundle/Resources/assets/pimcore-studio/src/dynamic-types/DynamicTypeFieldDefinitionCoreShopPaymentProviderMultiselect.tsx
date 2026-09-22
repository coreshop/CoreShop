/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { DynamicTypeFieldDefinitionCoreShopMultiselect } from '@coreshop/resource/src/dynamic-types/field-definitions'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'

export class DynamicTypeFieldDefinitionCoreShopPaymentProviderMultiselect extends DynamicTypeFieldDefinitionCoreShopMultiselect {
  id: string = 'coreShopPaymentProviderMultiselect'

  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_icon_payment_provider' }
  }
}
