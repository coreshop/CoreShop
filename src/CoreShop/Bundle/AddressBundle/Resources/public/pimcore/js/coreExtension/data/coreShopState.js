/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopState');
pimcore.object.classes.data.coreShopState = Class.create(coreshop.object.classes.data.select, {

    type: 'coreShopState',

    getTypeName: function () {
        return t('coreshop_state');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_state';
    }
});
