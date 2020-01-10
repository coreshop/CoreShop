/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

coreshop.product.unit.builder = Class.create(coreshop.product.unit.builder, {

    postSaveObject: function ($super, object, refreshedData) {
        $super(object, refreshedData);

        var defaultUnitDefinitionFieldSet = this.form.query('combo[cls~=default-unit-store]')[0].up('fieldset'),
            unitCombo = defaultUnitDefinitionFieldSet.query('combo[itemCls~=unit-store]')[0],
            lockButton = defaultUnitDefinitionFieldSet.query('button[cls~=default-unit-definition-lock]')[0];

        if (unitCombo) {
            unitCombo.setReadOnly(true);
        }

        if (lockButton) {
            lockButton.show();
        }
    },

    getUnitFormFields: function ($super, data, isDefault) {

        var unlockButton,
            fields = $super(data, isDefault);

        if (isDefault === false) {
            return fields;
        }

        unlockButton = {
            xtype: 'button',
            hidden: false,
            itemId: 'default-unit-unlock-change',
            tooltip: t('coreshop_product_unit_unit_definition_lock_button_message'),
            cls: 'default-unit-definition-lock coreshop-transparent-btn',
            iconCls: 'pimcore_icon_lock pimcore_material_icon',
            style: 'margin: 0 3px 0 0',
            handler: this.unlockAdditionalUnitCombo.bind(this, data)
        };

        if (data.hasOwnProperty('idValue') && data.idValue === null) {
            unlockButton.hidden = true;
        }

        fields.splice(1, 0, unlockButton);

        return fields;

    },

    unlockAdditionalUnitCombo: function (data, comp, ev) {

        var unitDefinitionId = null,
            unitCombo = comp.up('fieldset').query('combo[itemCls~=unit-store]')[0];

        if (this.data.hasOwnProperty('defaultUnitDefinition') && this.data.defaultUnitDefinition.id !== null) {
            unitDefinitionId = this.data.defaultUnitDefinition.id;
        }

        if (!unitDefinitionId) {
            unitCombo.setReadOnly(false);
            return;
        }

        Ext.Ajax.request({
            url: '/admin/coreshop/product/validation/unit-definitions-deletion',
            async: false,
            params: {
                id: this.objectId,
                unitDefinitionId: unitDefinitionId
            },
            success: function (response) {

                var resp = Ext.decode(response.responseText);

                unitCombo.setReadOnly(false);

                if (resp.success === false) {
                    unitCombo.setReadOnly(true);
                    Ext.Msg.alert(t('error'), resp.message);
                    return;
                }

                if (resp.status === 'locked') {
                    unitCombo.setReadOnly(true);
                    Ext.Msg.alert(t('error'), t('coreshop_product_unit_unit_definition_change_disabled'));
                }

            }.bind(this)
        });
    },

    onBeforeUnitComboRender: function ($super, data, comp) {

        if (comp.getName() !== 'defaultUnitDefinition.unit') {
            $super(data, comp);
            return;
        }

        if (!data.hasOwnProperty('idValue')) {
            return;
        }

        if (data.idValue === null) {
            return;
        }

        comp.setReadOnly(true);
    },

    onAdditionalUnitDelete: function ($super, fieldSet, compositeField) {

        var unitCombo = compositeField.query('combo[itemCls~=unit-store]')[0],
            removeButton = compositeField.query('button[itemId="additional-unit-delete-button"]')[0],
            unitId, unitDefinitionId;

        unitId = unitCombo.getValue();

        if (isNaN(unitId)) {
            $super(fieldSet, compositeField);
            return;
        }

        unitDefinitionId = this.getDefinitionIdFromUnitId(parseInt(unitId));

        if (unitDefinitionId === null) {
            $super(fieldSet, compositeField);
            return;
        }

        removeButton.disable();

        Ext.Ajax.request({
            url: '/admin/coreshop/product/validation/unit-definitions-deletion',
            params: {
                id: this.objectId,
                unitDefinitionId: unitDefinitionId
            },
            success: function (response) {

                var resp = Ext.decode(response.responseText);

                removeButton.enable();

                if (resp.success === false) {
                    Ext.Msg.alert(t('error'), resp.message);
                    return;
                }

                if (resp.status === 'locked') {
                    Ext.Msg.alert(t('error'), t('coreshop_product_unit_additional_unit_definition_deletion_disabled'));
                } else {
                    $super(fieldSet, compositeField);
                }

            }.bind(this),
            failure: function () {
                removeButton.enable();
            }
        });

    },

    getDefinitionIdFromUnitId: function (unitId) {

        var definitionId = null;

        if (!this.data.hasOwnProperty('unitDefinitions')) {
            return null;
        }

        if (!Ext.isArray(this.data.unitDefinitions)) {
            return null;
        }

        Ext.Array.each(this.data.unitDefinitions, function (definition) {
            if (definition.hasOwnProperty('unit') && definition.unit.hasOwnProperty('id')) {
                if (definition.unit.id === unitId) {
                    definitionId = definition.id
                }
            }
        });

        return definitionId;
    }

});
