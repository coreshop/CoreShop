/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

pimcore.registerNS('coreshop.filter.similarities');
pimcore.registerNS('coreshop.filter.similarities.abstract');

coreshop.filter.similarities.abstract = Class.create(coreshop.filter.abstract, {
    elementType: 'similarities',

    getDefaultItems: function () {
        return [
            this.getFieldsComboBox()
        ];
    },

    getItems: function () {
        return [
            {
                xtype: 'numberfield',
                fieldLabel: t('coreshop_filters_similarity_weight'),
                name: 'weight',
                width: 400,
                value: this.data.weight
            }
        ];
    }
});
