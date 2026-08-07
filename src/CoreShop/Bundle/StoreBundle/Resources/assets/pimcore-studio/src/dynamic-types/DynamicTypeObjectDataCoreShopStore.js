/**
 * CoreShop StoreBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */
var __rest = (this && this.__rest) || function (s, e) {
    var t = {};
    for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
        t[p] = s[p];
    if (s != null && typeof Object.getOwnPropertySymbols === "function")
        for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
            if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
                t[p[i]] = s[p[i]];
        }
    return t;
};
import React from 'react';
import { DynamicTypeObjectDataAbstractSelect, DynamicTypeFieldFilterMultiselect } from '@pimcore/studio-ui-bundle/modules/element';
import { StoreSelect } from '../components/StoreSelect';
export class DynamicTypeObjectDataCoreShopStore extends DynamicTypeObjectDataAbstractSelect {
    constructor() {
        super(...arguments);
        this.id = 'coreShopStore';
        this.dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect();
    }
    getObjectDataComponent(props) {
        var _a;
        const { name, noteditable, defaultFieldWidth } = props, rest = __rest(props, ["name", "noteditable", "defaultFieldWidth"]);
        return (React.createElement(StoreSelect, { value: rest.value, onChange: rest.onChange, disabled: noteditable === true, style: { width: (_a = defaultFieldWidth === null || defaultFieldWidth === void 0 ? void 0 : defaultFieldWidth.width) !== null && _a !== void 0 ? _a : '100%' } }));
    }
}
