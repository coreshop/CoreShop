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
import { Input, Select } from 'antd';
import { FormBuilder } from '@coreshop/studio-form/src/form-builder';
import { listSites } from './api';
import { CurrencySelectField } from "@coreshop/currency/src";
// Module-level cache for sites
let cachedSites = null;
let loadPromise = null;
const loadSites = async () => {
    if (cachedSites) {
        return cachedSites;
    }
    if (loadPromise) {
        return loadPromise;
    }
    loadPromise = (async () => {
        try {
            cachedSites = await listSites();
            return cachedSites;
        }
        catch (err) {
            console.error('Failed to load sites:', err);
            throw err;
        }
        finally {
            loadPromise = null;
        }
    })();
    return loadPromise;
};
export const clearSitesCache = () => {
    cachedSites = null;
    loadPromise = null;
};
export const createStoreFormBuilder = () => {
    const builder = new FormBuilder({
        fields: [
            {
                name: 'name',
                label: 'name',
                component: Input,
                required: true,
                rules: [{ required: true, message: 'Name is required' }],
                componentProps: { placeholder: 'Store name' }
            },
            {
                name: 'siteId',
                label: 'coreshop_store_site',
                component: Select,
                componentProps: async () => {
                    const sites = await loadSites();
                    return {
                        options: sites.map(site => ({ value: site.id, label: site.name })),
                        placeholder: 'Select a site',
                        allowClear: true,
                        showSearch: true,
                        filterOption: (input, option) => { var _a; return ((_a = option === null || option === void 0 ? void 0 : option.label) !== null && _a !== void 0 ? _a : '').toLowerCase().includes(input.toLowerCase()); }
                    };
                }
            },
            {
                name: 'template',
                label: 'coreshop_store_template',
                component: Input,
                componentProps: { placeholder: 'Template name' }
            },
            {
                name: 'currency',
                label: 'coreshop_currency',
                component: CurrencySelectField,
                componentProps: {
                    allowClear: true
                }
            }
        ]
    });
    return builder;
};
