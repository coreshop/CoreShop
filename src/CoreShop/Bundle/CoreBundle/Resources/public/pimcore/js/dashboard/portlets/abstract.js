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

pimcore.registerNS('coreshop.portlet.abstract');
coreshop.portlet.abstract = Class.create(pimcore.layout.portlets.abstract, {
    download: function () {
        var me = this;

        var url = '/admin/coreshop/portlet/export?portlet=' + me.portletType;
        var filterParams = me.getFilterParams();

        url += '&' + Ext.urlEncode(filterParams);

        pimcore.helpers.download(url);
    },

    getFilterParams: function() {
        return {};
    }
});

