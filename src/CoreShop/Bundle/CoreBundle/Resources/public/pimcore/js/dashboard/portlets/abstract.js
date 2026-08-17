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

pimcore.registerNS('coreshop.portlet.abstract');
coreshop.portlet.abstract = Class.create(pimcore.layout.portlets.abstract, {
    download: function () {
        var me = this;

        var filterParams = me.getFilterParams();
        filterParams['portlet'] = me.portletType;

        pimcore.helpers.download(Routing.generate('coreshop_admin_report_portlet', filterParams));
    },

    getFilterParams: function() {
        return {};
    }
});

