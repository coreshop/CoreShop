import React from 'react';
import { Form, Select } from 'antd';
import { useTranslation } from 'react-i18next';
import { storeApi } from '../modules/stores/api';
// Module-level cache to avoid multiple API calls
let cachedOptions = null;
let loadPromise = null;
// Export for use in StoresCondition
export const loadStores = async () => {
    // Return cached data if available
    if (cachedOptions) {
        return cachedOptions;
    }
    // If already loading, return the existing promise
    if (loadPromise) {
        return loadPromise;
    }
    // Start new load
    loadPromise = (async () => {
        try {
            const rows = await storeApi.list();
            const list = Array.isArray(rows) ? rows : [];
            cachedOptions = list
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
// Export function to clear cache if needed
export const clearStoreCache = () => {
    cachedOptions = null;
    loadPromise = null;
};
export const StoreMultiSelect = ({ name = 'stores', label, labelKey, placeholder, disabled, size, className, style, value, onChange, }) => {
    const [options, setOptions] = React.useState(cachedOptions || []);
    const [loading, setLoading] = React.useState(!cachedOptions);
    const { t } = useTranslation();
    React.useEffect(() => {
        void (async () => {
            if (!cachedOptions) {
                setLoading(true);
            }
            try {
                const opts = await loadStores();
                setOptions(opts);
            }
            finally {
                setLoading(false);
            }
        })();
    }, []);
    const computedLabel = label !== null && label !== void 0 ? label : (labelKey ? t(labelKey) : t('coreshop_stores', { defaultValue: 'Stores' }));
    const computedPlaceholder = placeholder !== null && placeholder !== void 0 ? placeholder : t('coreshop.ui.select', { defaultValue: 'Select' });
    const selectProps = {
        mode: 'multiple',
        options,
        loading,
        placeholder: computedPlaceholder,
        disabled,
        size,
        showSearch: true,
        className,
        style,
        optionFilterProp: 'label',
        maxTagCount: 'responsive',
    };
    // If value/onChange provided, use controlled mode (bypass Form.Item)
    if (value !== undefined || onChange !== undefined) {
        selectProps.value = value;
        selectProps.onChange = onChange;
    }
    return (React.createElement(Form.Item, { label: computedLabel, name: name },
        React.createElement(Select, Object.assign({}, selectProps))));
};
