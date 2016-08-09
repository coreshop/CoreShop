/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.rules.actions.freeShipping');

pimcore.plugin.coreshop.rules.actions.freeShipping = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'freeShipping',

    getForm : function () {

        this.form = new Ext.form.FieldSet({
            type: 'FreeShipping',
            forceLayout: true
        });

        return this.form;
    }
});
