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

pimcore.registerNS('pimcore.plugin.coreshop.indexes.objecttype.mysql.abstract');

pimcore.plugin.coreshop.indexes.objecttype.mysql.field = Class.create(pimcore.plugin.coreshop.indexes.objecttype.abstract, {

    getObjectTypeItems : function (record) {
        return [new Ext.form.TextField({
            fieldLabel : t('coreshop_index_field_type'),
            name : 'columnType',
            length : 255,
            width : 200,
            value : record.data.columnType
        })];
    }

});
