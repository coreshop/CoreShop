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

coreshop.helpers.requestNicePathData = function (targets, responseHandler) {
    var elementData = Ext.encode(targets);

    Ext.Ajax.request({
        method: 'POST',
        url: "/admin/coreshop/helper/get-nice-path",
        params: {
            targets: elementData
        },
        success: function (response) {
            try {
                var rdata = Ext.decode(response.responseText);
                if (rdata.success) {

                    var responseData = rdata.data;
                    responseHandler(responseData);
                }
            } catch (e) {
                console.log(e);
            }
        }.bind(this)
    });
};
