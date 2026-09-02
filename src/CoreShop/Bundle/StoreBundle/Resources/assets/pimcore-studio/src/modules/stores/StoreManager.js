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
import { EntityTabbedManager } from '@coreshop/resource/src/entities';
import { useTranslation } from 'react-i18next';
import { storeApi } from './api';
import { StoreForm } from './StoreForm';
import { useFormModal } from '@pimcore/studio-ui-bundle/components';
export const StoreManager = () => {
    const { t } = useTranslation();
    const modal = useFormModal();
    return (React.createElement(EntityTabbedManager, { api: storeApi, dragType: "coreshop:store", leftRootTitle: t('coreshop_stores', { defaultValue: 'Stores' }), getTitle: (li, data) => { var _a, _b, _c, _d; return (_b = (_a = data === null || data === void 0 ? void 0 : data.name) !== null && _a !== void 0 ? _a : li === null || li === void 0 ? void 0 : li.name) !== null && _b !== void 0 ? _b : `Store #${(_d = (_c = data === null || data === void 0 ? void 0 : data.id) !== null && _c !== void 0 ? _c : li === null || li === void 0 ? void 0 : li.id) !== null && _d !== void 0 ? _d : ''}`; }, buildSavePayload: (data) => data, onAdd: async () => await new Promise((resolve) => {
            modal.input({
                title: t('coreshop_store_add', { defaultValue: 'Add Store' }),
                label: t('coreshop_name', { defaultValue: 'Name' }),
                rule: { required: true, message: t('coreshop_name_required', { defaultValue: 'Name is required' }) },
                onOk: async (nameValue) => {
                    const res = await storeApi.add({ name: nameValue });
                    if (res.data.id !== undefined) {
                        resolve(res.data.id);
                    }
                }
            });
        }), renderDetail: (data, setData) => (React.createElement(StoreForm, { data: data, onChange: setData })) }));
};
