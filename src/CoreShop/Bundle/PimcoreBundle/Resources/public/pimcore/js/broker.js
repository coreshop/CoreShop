/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('coreshop.broker');
coreshop.broker = {

    _listeners: {},

    initialize: function () {

    },

    fireEvent: function () {
        var name = arguments[0];
        if (coreshop.broker._listeners[name] === undefined) {
            return;
        }

        var list = coreshop.broker._listeners[name];

        //copy arguments
        var args = [];
        for (var j = 1; j < arguments.length; j++) {
            args.push(arguments[j]);
        }

        for (var i = 0; i < list.length; i++) {
            list[i].func.apply(list[i].scope, args);

            if (list[i].once) {
                list.splice(i, 1);
            }
        }
    },

    removeListener: function (name, func) {
        if (coreshop.broker._listeners[name] === undefined) {
            return;
        }

        var list = coreshop.broker._listeners[name];
        for (var i = 0; i < list.length; i++) {
            if (list[i].func === func) {
                list.splice(i, 1);
            }
        }

        if (list.length === 0) {
            delete coreshop.broker._listeners[name];
        }
    },

    addListener: function (name, func, scope, once) {
        if (coreshop.broker._listeners[name] === undefined) {
            coreshop.broker._listeners[name] = [];
        }

        coreshop.broker._listeners[name].push({
            func: func,
            scope: scope,
            once: Ext.isDefined(once) ? once : false
        });
    },

    addListenerOnce: function (name, func, scope) {
        coreshop.broker.addListener(name, func, scope, true);
    }
};