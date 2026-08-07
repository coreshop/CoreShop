/**
 * CoreShop MoneyBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import {
  DynamicTypeObjectDataAbstractNumeric,
  DynamicTypeFieldFilterNumber
} from '@pimcore/studio-ui-bundle/modules/element'

export class DynamicTypeObjectDataCoreShopMoney extends DynamicTypeObjectDataAbstractNumeric {
  readonly id = 'coreShopMoney'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterNumber()
}
