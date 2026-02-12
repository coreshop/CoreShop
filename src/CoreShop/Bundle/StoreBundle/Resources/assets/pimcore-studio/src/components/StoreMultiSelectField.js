import React from 'react';
import { Select } from 'antd';
import { useTranslation } from 'react-i18next';
import { storeApi } from '../modules/stores/api';
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity';
let cachedOptions = null;
let loadPromise = null;
const loadStores = async () => {
    if (cachedOptions)
        return cachedOptions;
    if (loadPromise)
        return loadPromise;
    loadPromise = (async () => {
        try {
            const rows = await storeApi.list();
            cachedOptions = (Array.isArray(rows) ? rows : [])
                .map((r) => { var _a; return ({ value: r.id, label: (_a = r.name) !== null && _a !== void 0 ? _a : String(r.id) }); })
                .filter((o) => o.value != null && o.label);
            return cachedOptions;
        }
        catch (err) {
            console.error('Failed to load stores:', err);
            return [];
        }
        finally {
            loadPromise = null;
        }
    })();
    return loadPromise;
};
export const clearStoreCache = () => {
    cachedOptions = null;
    loadPromise = null;
};
export const StoreMultiSelectField = (props) => {
    var _a;
    const [options, setOptions] = React.useState(cachedOptions || []);
    const [loading, setLoading] = React.useState(!cachedOptions);
    const { t } = useTranslation();
    React.useEffect(() => {
        void (async () => {
            if (!cachedOptions)
                setLoading(true);
            try {
                const opts = await loadStores();
                setOptions(opts);
            }
            finally {
                setLoading(false);
            }
        })();
    }, []);
    return (React.createElement(DroppableEntity, { accept: 'coreshop:store', isValidData: (info) => { var _a; return typeof ((_a = info === null || info === void 0 ? void 0 : info.data) === null || _a === void 0 ? void 0 : _a.id) === 'number'; }, onDrop: (info) => {
            var _a;
            if (props.onChange && ((_a = info === null || info === void 0 ? void 0 : info.data) === null || _a === void 0 ? void 0 : _a.id)) {
                const currentValue = props.value || [];
                const newValue = Array.isArray(currentValue)
                    ? [...currentValue, info.data.id]
                    : [info.data.id];
                const event = { target: { value: newValue } };
                props.onChange(newValue, event);
            }
        } },
        React.createElement(Select, Object.assign({}, props, { mode: "multiple", loading: loading, options: options, placeholder: (_a = props.placeholder) !== null && _a !== void 0 ? _a : t('coreshop.ui.select', { defaultValue: 'Select' }), showSearch: true, optionFilterProp: "label" }))));
};
