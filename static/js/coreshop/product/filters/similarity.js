/**
 * CoreShop
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2016 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

pimcore.registerNS('pimcore.plugin.coreshop.filters.similarity');

pimcore.plugin.coreshop.filters.similarity = Class.create({

    type : null,

    initialize : function (parent, similarities) {
        this.parent = parent;
        this.similarities = similarities;
    },

    getFieldsStore : function () {
        return this.parent.getFieldsForIndex();
    },

    getLayout: function () {
        // init
        var _this = this;
        var addMenu = [];

        Ext.each(this.similarities, function (similarity) {

            if (similarity == 'abstract')
                return;

            addMenu.push({
                iconCls: 'coreshop_product_filters_icon_similarities_' + similarity,
                text: t('coreshop_product_filters_' + similarity),
                handler: _this.addSimilarity.bind(_this, similarity, {})
            });

        });

        this.fieldsContainer = new Ext.Panel({
            iconCls: 'coreshop_product_similarity',
            title: t('coreshop_product_similarity'),
            autoScroll: true,
            style : 'padding: 10px',
            forceLayout: true,
            tbar: [{
                iconCls: 'pimcore_icon_add',
                menu: addMenu
            }],
            border: false
        });

        return this.fieldsContainer;
    },

    disable : function () {
        this.fieldsContainer.disable();
    },

    enable : function () {
        this.fieldsContainer.enable();
    },

    addSimilarity: function (type, data) {
        // create similarity
        var item = new pimcore.plugin.coreshop.filters.similarities[type](this, data);

        // add logic for brackets
        var tab = this;

        this.fieldsContainer.add(item.getLayout());
        this.fieldsContainer.updateLayout();
    },

    getData : function () {
        // get defined similarities
        var similarityData = [];
        var similarities = this.fieldsContainer.items.getRange();
        for (var i = 0; i < similarities.length; i++) {
            var similarityItem = similarities[i];
            var similarityClass = similarityItem.xparent;
            var form = similarityClass.form;

            var similarity = form.form.getFieldValues();
            similarity['type'] = similarities[i].xparent.type;

            similarityData.push(similarity);
        }

        return similarityData;
    }
});
