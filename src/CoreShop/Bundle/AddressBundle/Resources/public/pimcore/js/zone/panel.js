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

pimcore.registerNS('coreshop.zone.panel');
coreshop.zone.panel = Class.create(coreshop.resource.panel, {

    /**
     * @var string
     */
    layoutId: 'coreshop_zones_panel',
    storeId: 'coreshop_zones',
    iconCls: 'coreshop_icon_zone',
    type: 'coreshop_zones',

    routing: {
        add: 'coreshop_zone_add',
        delete: 'coreshop_zone_delete',
        get: 'coreshop_zone_get',
        list: 'coreshop_zone_list'
    },

    getItemClass: function() {
        return coreshop.zone.item;
    }
});
