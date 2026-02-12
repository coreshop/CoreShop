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
import { Select } from 'antd';
import { DynamicTypeObjectDataAbstractMultiSelect, DynamicTypeFieldFilterMultiselect } from '@pimcore/studio-ui-bundle/modules/element';
import { loadStores, clearStoreCache } from '../components/StoreMultiSelect';
// Re-export for external use
export { clearStoreCache };
const StoreMultiSelectInner = ({ value, onChange, disabled, style }) => {
    const [options, setOptions] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    React.useEffect(() => {
        void (async () => {
            try {
                const opts = await loadStores();
                setOptions(opts);
            }
            finally {
                setLoading(false);
            }
        })();
    }, []);
    return (React.createElement(Select, { mode: "multiple", value: value === null || value === void 0 ? void 0 : value.map(v => typeof v === 'string' ? Number(v) : v), onChange: onChange, options: options, loading: loading, disabled: disabled, style: style, showSearch: true, optionFilterProp: "label", maxTagCount: "responsive" }));
};
export class DynamicTypeObjectDataCoreShopStoreMultiselect extends DynamicTypeObjectDataAbstractMultiSelect {
    constructor() {
        super(...arguments);
        this.id = 'coreShopStoreMultiselect';
        this.dynamicTypeFieldFilterType = new DynamicTypeFieldFilterMultiselect();
    }
    getObjectDataComponent(props) {
        var _a;
        const { name, noteditable, defaultFieldWidth } = props, rest = __rest(props, ["name", "noteditable", "defaultFieldWidth"]);
        return (React.createElement(StoreMultiSelectInner, { value: rest.value, onChange: rest.onChange, disabled: noteditable === true, style: { width: (_a = defaultFieldWidth === null || defaultFieldWidth === void 0 ? void 0 : defaultFieldWidth.width) !== null && _a !== void 0 ? _a : '100%' } }));
    }
}
