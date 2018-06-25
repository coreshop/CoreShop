/**
 * Import Definitions.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2016-2018 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/ImportDefinitions/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('coreshop.index.interpreters.nested');

coreshop.index.interpreters.nested = Class.create(coreshop.index.interpreters.abstract, {
    getForm: function (record, interpreterConfig) {
        // init
        var _this = this;
        var addMenu = [];
        var records = pimcore.globalmanager.get('coreshop_index_interpreters').getRange().map(function(interpreter) {return interpreter.get('type')});

        Ext.each(records, function (interpreter) {
            if (interpreter === 'abstract')
                return;

            addMenu.push({
                text: interpreter,
                handler: _this.addInterpreter.bind(_this, interpreter, record, {})
            });

        });

        this.interpreterContainer = new Ext.Panel({
            autoScroll: true,
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        if (interpreterConfig && interpreterConfig.interpreters) {
            Ext.each(interpreterConfig.interpreters, function (interpreter) {
                this.addInterpreter(interpreter.type, record, interpreter.interpreterConfig);
            }.bind(this));
        }

        return this.interpreterContainer;
    },

    destroy: function () {
        if (this.interpreterContainer) {
            this.interpreterContainer.destroy();
        }
    },

    getInterpreterClassItem: function (type) {
        if (Object.keys(coreshop.index.interpreters).indexOf(type) >= 0) {
            return coreshop.index.interpreters[type];
        }

        return coreshop.index.interpreters.empty;
    },

    addInterpreter: function (type, record, config) {
        // create condition
        var interpreterClass = this.getInterpreterClassItem(type);
        var item = new interpreterClass();
        var container = new coreshop.index.interpreters.nestedcontainer(this, type, item);

        this.interpreterContainer.add(container.getLayout(type, record, config));
        this.interpreterContainer.updateLayout();
    },

    isValid: function() {
        var interpreters = this.interpreterContainer.items.getRange();
        for (var i = 0; i < interpreters.length; i++) {
            var interpreterItem = interpreters[i];
            var interpreterClass = interpreterItem.xparent.interpreterItem;

            if (!interpreterClass.isValid()) {
                return false;
            }
        }

        return true;
    },

    getInterpreterData: function () {
        // get defined conditions
        var interpreterData = [];
        var interpreters = this.interpreterContainer.items.getRange();
        for (var i = 0; i < interpreters.length; i++) {
            var configuration = {};
            var interpreter = {};

            var interpreterItem = interpreters[i];
            var interpreterClass = interpreterItem.xparent.interpreterItem;

            interpreter['interpreterConfig'] = interpreterClass.getInterpreterData();
            interpreter['type'] = interpreters[i].xparent.type;

            interpreterData.push(interpreter);
        }

        return {
            interpreters: interpreterData
        };
    }
});
