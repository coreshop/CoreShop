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

pimcore.registerNS('coreshop.object.classes.data.coreShopRelation');
pimcore.object.classes.data.coreShopRelation = Class.create(coreshop.object.classes.data.data, {

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
        this.type = "coreShopRelation";

        this.initData(initData);

        this.treeNode = treeNode;
    },

    getTypeName: function () {
        return t("coreshop_coreshop_relation");
    },

    getGroup: function () {
        return "coreshop";
    },

    getIconClass: function () {
        return "pimcore_icon_manyToOneRelation";
    },

    getLayout: function ($super) {

        $super();

        this.specificPanel.removeAll();
        this.uniqeFieldId = uniqid();

        var stacks = coreshop.full_stack;

        this.specificPanel.add([
            {
                xtype:'fieldset',
                title: t('layout'),
                collapsible: false,
                autoHeight:true,
                labelWidth: 100,
                items :[
                    {
                        xtype: "numberfield",
                        fieldLabel: t("width"),
                        name: "width",
                        value: this.datax.width
                    },
                    {
                        xtype: 'textfield',
                        width: 600,
                        fieldLabel: t("path_formatter_service"),
                        name: 'pathFormatterClass',
                        value: this.datax.pathFormatterClass
                    }
                ]
            },
            {
                xtype:'fieldset',
                title: t('coreshop_stacks') ,
                disabled: this.isInCustomLayoutEditor(),
                collapsible: false,
                autoHeight:true,
                labelWidth: 100,
                items :[
                    {
                        xtype: 'combo',
                        fieldLabel: t("coreshop_allowed_stack"),
                        name: "classes",
                        id: 'coreshop_relation_stack_' + this.uniqeFieldId,
                        value: this.datax.stack,
                        store: stacks,
                        width: 400
                    }
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
                    width: source.datax.width,
                    relationType: source.datax.relationType,
                    remoteOwner: source.datax.remoteOwner,
                    stack: source.datax.stack,
                    pathFormatterClass: source.datax.pathFormatterClass
                }
            );
        }
    }
});
