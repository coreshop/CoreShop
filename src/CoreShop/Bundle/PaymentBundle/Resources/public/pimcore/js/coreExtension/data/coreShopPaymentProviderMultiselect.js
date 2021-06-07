/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopPaymentProviderMultiselect');
pimcore.object.classes.data.coreShopPaymentProviderMultiselect = Class.create(coreshop.object.classes.data.dataMultiselect, {

    type: 'coreShopPaymentProviderMultiselect',

    getTypeName: function () {
        return t('coreshop_payment_provider_multiselect');
    },

    getIconClass: function () {
        return 'coreshop_icon_payment_provider';
    },

    getGroup: function () {
        return 'coreshop';
    }
});
