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

pimcore.registerNS('coreshop.shippingrule.conditions.postcodes');

coreshop.shippingrule.conditions.postcodes = Class.create(coreshop.rules.conditions.abstract, {
    type: 'postcodes',

    getForm: function () {

        var postCodesValues = '';
        var exclusionValue = false;

        if (this.data) {
            if (this.data.postcodes) {
                postCodesValues = this.data.postcodes;
            }

            if (this.data.exclusion) {
                exclusionValue = this.data.exclusion;
            }
        }

        var info = new Ext.panel.Panel({
            border: false,
            html: t('coreshop_condition_postcodes_info'),
            bodyPadding: '0 0 20px 0'
        });

        var exclusion = new Ext.form.Checkbox({
            fieldLabel: t('coreshop_condition_postcodes_exclusion'),
            name: 'exclusion',
            checked: exclusionValue
        });

        var postcodes = new Ext.form.TextArea({
            fieldLabel: t('coreshop_condition_postcodes'),
            name: 'postcodes',
            value: postCodesValues
        });

        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                info, postcodes, exclusion
            ]
        });

        return this.form;
    }
});
