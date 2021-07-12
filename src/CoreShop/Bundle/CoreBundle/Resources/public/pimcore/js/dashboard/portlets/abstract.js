/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
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

