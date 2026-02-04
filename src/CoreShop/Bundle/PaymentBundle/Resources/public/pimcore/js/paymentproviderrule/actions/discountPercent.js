/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.paymentproviderrule.actions.discountPercent');
coreshop.paymentproviderrule.actions.discountPercent = Class.create(coreshop.paymentproviderrule.actions.additionPercent, {
    type: 'discountPercent'
});
