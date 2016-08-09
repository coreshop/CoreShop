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

pimcore.registerNS('pimcore.plugin.coreshop.indexes.getters.brick');

pimcore.plugin.coreshop.indexes.getters.brick = Class.create(pimcore.plugin.coreshop.indexes.getters.abstract, {

    getLayout : function (record) {
        return [
            {
                xtype : 'textfield',
                fieldLabel : t('coreshop_index_field_brickfield'),
                name : 'brickField',
                length : 255,
                value : record.data.getterConfig ? record.data.getterConfig.brickField : null
            }
        ];
    }

});
