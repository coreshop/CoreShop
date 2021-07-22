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

pimcore.registerNS('coreshop.shippingrule.conditions.dimension');

coreshop.shippingrule.conditions.dimension = Class.create(coreshop.rules.conditions.abstract, {
    type: 'dimension',

    getForm: function () {

        var heightValue = 0;
        var widthValue = 0;
        var depthValue = 0;
        var me = this;

        if (this.data) {
            if (this.data.height) {
                heightValue = this.data.height;
            }

            if (this.data.width) {
                widthValue = this.data.width;
            }

            if (this.data.depth) {
                depthValue = this.data.depth;
            }
        }

        var height = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_dimension_height'),
            name: 'height',
            value: heightValue,
            minValue: 0,
            decimalPrecision: 0,
            step: 1
        });

        var width = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_dimension_width'),
            name: 'width',
            value: widthValue,
            minValue: 0,
            decimalPrecision: 0,
            step: 1
        });

        var depth = new Ext.form.NumberField({
            fieldLabel: t('coreshop_condition_dimension_depth'),
            name: 'depth',
            value: depthValue,
            minValue: 0,
            decimalPrecision: 0,
            step: 1
        });

        this.form = Ext.create('Ext.form.FieldSet', {
            items: [
                height, width, depth
            ]
        });

        return this.form;
    }
});
