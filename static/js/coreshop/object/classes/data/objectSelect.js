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


pimcore.registerNS("pimcore.object.classes.data.objectSelect");
pimcore.object.classes.data.objectSelect = Class.create(pimcore.object.classes.data.data, {

    type: "objectSelect",
    /**
     * define where this datatype is allowed
     */
    allowIn: {
        object: true,
        objectbrick: true,
        fieldcollection: true,
        localizedfield: true
    },

    initialize: function (treeNode, initData) {
        this.type = "objectSelect";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_select_href");
    },

    getGroup: function () {
        return "relation";
    },

    getIconClass: function () {
        return "pimcore_icon_select";
    },

    getLayout: function ($super) {
        $super();

        this.specificPanel.removeAll();

        this.uniqeFieldId = uniqid();

        var i;

        var allowedClasses = [];
        if(typeof this.datax.classes == "object") {
            // this is when it comes from the server
            for(i=0; i<this.datax.classes.length; i++) {
                allowedClasses.push(this.datax.classes[i]["classes"]);
            }
        } else if(typeof this.datax.classes == "string") {
            // this is when it comes from the local store
            allowedClasses = this.datax.classes.split(",");
        }

        var classesStore = new Ext.data.JsonStore({
            autoDestroy: true,
            url: '/admin/class/get-tree',
            fields: ["text"]
        });
        classesStore.load({
            "callback": function (allowedClasses) {
                Ext.getCmp('class_allowed_object_classes_' + this.uniqeFieldId).setValue(allowedClasses.join(","));
            }.bind(this, allowedClasses)
        });

        this.specificPanel.add([
            {
                xtype:'fieldset',
                title: t('object_restrictions') ,
                disabled: this.isInCustomLayoutEditor(),
                collapsible: false,
                autoHeight:true,
                labelWidth: 100,
                items :[
                    new Ext.ux.form.MultiSelect({
                        fieldLabel: t("allowed_classes") + '<br />' + t('allowed_types_hint'),
                        name: "classes",
                        id: 'class_allowed_object_classes_' + this.uniqeFieldId,
                        value: allowedClasses.join(","),
                        displayField: "text",
                        valueField: "text",
                        store: classesStore,
                        width: 300
                    })
                ]
            }


        ]);

        return this.layout;
    },

    applySpecialData: function(source) {
        if (source.datax) {
            if (!this.datax) {
                this.datax =  {};
            }
            Ext.apply(this.datax,
                {
                    classes: source.datax.classes
                });
        }
    }

});
