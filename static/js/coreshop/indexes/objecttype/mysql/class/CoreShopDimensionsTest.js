/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS("pimcore.plugin.coreshop.indexes.objecttype.mysql.class.CoreShopDimensionTest");

pimcore.plugin.coreshop.indexes.objecttype.mysql.class.CoreShopDimensionTest = Class.create(pimcore.plugin.coreshop.indexes.objecttype.mysql.objectbricks, {
    getObjectTypeItems : function(record) {
        var fields = pimcore.plugin.coreshop.indexes.objecttype.mysql.objectbricks.prototype.getObjectTypeItems.call(this, record);

        fields.push(new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_brickfield2'),
            name : 'brickField2',
            length : 255,
            width : 200,
            value : record.data.brickField2
        }));

        return fields;
    }
});