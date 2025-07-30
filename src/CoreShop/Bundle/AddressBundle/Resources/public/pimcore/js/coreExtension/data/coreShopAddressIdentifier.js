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

pimcore.registerNS('pimcore.object.classes.data.coreShopAddressIdentifier');
pimcore.object.classes.data.coreShopAddressIdentifier = Class.create(coreshop.object.classes.data.select, {

    type: 'coreShopAddressIdentifier',

    getTypeName: function () {
        return t('coreshop_address_identifier');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_address_identifier';
    }
});
