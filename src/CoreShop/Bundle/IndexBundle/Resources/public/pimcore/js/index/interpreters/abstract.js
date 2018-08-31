/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.index.interpreters');
pimcore.registerNS('coreshop.index.interpreters.abstract');

coreshop.index.interpreters.abstract = Class.create({

    getLayout: function (record, interpreterConfig) {
        return [];
    },

    getForm: function(record, interpreterConfig) {
        if (!this.form) {
            this.form = new Ext.form.FormPanel({
                defaults: {anchor: '90%'},
                layout: 'form',
                items: this.getLayout(record, interpreterConfig)
            });
        }

        return this.form;
    },

    isValid: function() {
        return this.getForm().getForm().isValid()
    },

    getInterpreterData: function() {
        return this.form.getForm().getFieldValues();
    },
});
