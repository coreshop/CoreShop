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

pimcore.registerNS('pimcore.plugin.coreshop.rules.conditions.customerGroups');

pimcore.plugin.coreshop.rules.conditions.customerGroups = Class.create(pimcore.plugin.coreshop.rules.conditions.abstract, {

    type : 'customerGroups',

    customerGroups : null,

    getForm : function () {
        this.customerGroups = new pimcore.plugin.coreshop.object.objectMultihref(this.data ? this.data.customerGroups : [], {
            classes: [
                { classes: coreshop.settings.classMapping.customerGroup }
            ],
            name: 'customerGroups',
            title: '',
            height: 200,
            width: 500,
            columns: [],

            columnType: null,
            datatype: 'data',
            fieldtype: 'objects'
        });

        this.form = new Ext.form.FieldSet({
            items : [
                this.customerGroups.getLayoutEdit()
            ]
        });

        return this.form;
    },

    getValues : function () {
        return {
            customerGroups : this.customerGroups.getValue()
        };
    }
});
