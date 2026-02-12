/**
 * CoreShop StoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */
import React from 'react';
import { Select } from 'antd';
import { storeApi } from '../modules/stores/api';
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity';
// Module-level cache to avoid multiple API calls
let cachedOptions = null;
let loadPromise = null;
const loadStores = async () => {
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
            const stores = await storeApi.list();
            const result = stores.map(store => ({
                value: store.id,
                label: store.name
            }));
            cachedOptions = result;
            return result;
        }
        catch (err) {
            console.error('Failed to load stores:', err);
            throw err;
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
export const StoreSelect = (props) => {
    const [options, setOptions] = React.useState(cachedOptions || []);
    const [loading, setLoading] = React.useState(!cachedOptions);
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
    return (React.createElement(DroppableEntity, { accept: "coreshop:store", isValidData: (info) => { var _a; return typeof ((_a = info === null || info === void 0 ? void 0 : info.data) === null || _a === void 0 ? void 0 : _a.id) === 'number'; }, onDrop: (info) => {
            var _a;
            if (props.onChange && ((_a = info === null || info === void 0 ? void 0 : info.data) === null || _a === void 0 ? void 0 : _a.id)) {
                const event = { target: { value: info.data.id } };
                props.onChange(info.data.id, event);
            }
        } },
        React.createElement(Select, Object.assign({}, props, { loading: loading, options: options }))));
};
