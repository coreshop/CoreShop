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

pimcore.registerNS('coreshop.notification.rule.conditions.comment');

coreshop.notification.rule.conditions.comment = Class.create(coreshop.rules.conditions.abstract, {
    type: 'comment',
    getForm: function () {
        this.form = new Ext.form.Panel({
            items: [
                {
                    xtype: 'combo',
                    fieldLabel: t('coreshop_condition_comment_action'),
                    typeAhead: false,
                    editable: false,
                    width: 500,
                    value: this.data ? this.data.commentAction : null,
                    store: [['create', t('coreshop_condition_comment_action_create')]],
                    forceSelection: true,
                    triggerAction: 'all',
                    name: 'commentAction',
                    queryMode: 'local'
                }
            ]
        });

        return this.form;
    }
});
