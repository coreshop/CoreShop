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

pimcore.registerNS('coreshop.helpers.x');

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

coreshop.helpers.convertDotNotationToObject = function (data) {
    var obj = {};

    Object.keys(data).forEach(function (key) {  //loop through the keys in the object
        var val = data[key];  //grab the value of this key
        var step = obj;  //reference the object that holds the values
        key.split(".").forEach(function (part, index, arr) {   //split the parts and loop
            if (index === arr.length - 1) {  //If we are at the last index, than we set the value
                step[part] = val;
            } else if (step[part] === undefined) {  //If we have not seen this key before, create an object
                step[part] = {};
            }
            step = step[part];  //Step up the object we are referencing
        });
    });

    return obj;
};
