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

pimcore.registerNS('pimcore.plugin.coreshop.mail.rules.actions.mail');

pimcore.plugin.coreshop.mail.rules.actions.mail = Class.create(pimcore.plugin.coreshop.rules.actions.abstract, {

    type : 'mail',

    getForm : function () {
        var me = this;

        if (this.data) {

        }

        this.form = new Ext.form.FieldSet({
            items : [

            ]
        });

        return this.form;
    }
});
