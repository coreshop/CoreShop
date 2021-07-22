/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('pimcore.object.classes.data.coreShopTaxRuleGroup');
pimcore.object.classes.data.coreShopTaxRuleGroup = Class.create(coreshop.object.classes.data.select, {

    type: 'coreShopTaxRuleGroup',

    getTypeName: function () {
        return t('coreshop_tax_rule_group');
    },

    getGroup: function () {
        return 'coreshop';
    },

    getIconClass: function () {
        return 'coreshop_icon_tax_rule_groups';
    }
});
