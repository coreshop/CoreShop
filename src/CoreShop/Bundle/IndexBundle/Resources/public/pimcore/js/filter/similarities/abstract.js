/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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
