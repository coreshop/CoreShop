/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.order.detail');
coreshop.order.order.detail = Class.create(coreshop.order.order.detail, {

    getHeader: function ($super) {
        if (!this.headerPanel) {
            var header = $super();

            header.add({
                xtype: 'panel',
                html: t('coreshop_store') + '<br/><span class="coreshop_order_big">' + this.order.store.name + '</span>',
                bodyPadding: 20,
                flex: 1
            });

            return header;
        }

        return this.headerPanel;
    }
});
