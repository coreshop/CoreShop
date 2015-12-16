/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.coreshop.org/license
 *
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     New BSD License
 */

pimcore.registerNS("pimcore.plugin.coreshop.broker");
pimcore.plugin.coreshop.broker = {

    _listeners: {},

    initialize: function() {

    },

    fireEvent: function() {
        var name = arguments[0];
        if (this._listeners[name] === undefined) {
            return;
        }
        var list = this._listeners[name];
        //copy arguments
        var args = [];
        for(var j = 1; j < arguments.length; j++) {
            args.push(arguments[j]);
        }
        for(var i = 0; i < list.length; i++) {
            list[i].func.apply(list[i].scope, args);
        }
    },

    removeListener: function(name, func) {
        if (this._listeners[name] === undefined) {
            return;
        }
        var list = this._listeners[name];
        for(var i = 0; i < list.length; i++) {
            if (list[i].func === func) {
                list.splice(i,1);
            }
        }
        if (list.length === 0) {
            delete this._listeners[name];
        }
    },

    addListener: function(name, func, scope) {
        if (this._listeners[name] === undefined) {
            this._listeners[name] = [];
        }
        this._listeners[name].push({
            func: func,
            scope: scope
        });
    }
};