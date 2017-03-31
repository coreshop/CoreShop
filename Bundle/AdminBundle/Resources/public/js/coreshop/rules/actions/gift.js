/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.rules.actions.gift');

pimcore.plugin.coreshop.rules.actions.gift = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'gift',

    getForm : function () {
        this.gift = new pimcore.plugin.coreshop.object.elementHref({
            id : this.data ? this.data.gift : null,
            type : 'object',
            subtype : coreshop.settings.classMapping.product
        }, {
            objectsAllowed : true,
            classes : [{
                classes : coreshop.settings.classMapping.product
            }],
            name: 'gift',
            title: t('coreshop_action_gift_product')
        });

        this.form = new Ext.form.Panel({
            items : [
                this.gift.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues : function() {
        return {
            gift : this.gift.getValue()
        };
    }
});
