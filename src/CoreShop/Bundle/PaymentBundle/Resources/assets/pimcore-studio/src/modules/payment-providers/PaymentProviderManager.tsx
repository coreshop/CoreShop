/**
 * CoreShop PaymentBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { EntityTabbedManager } from '@coreshop/resource/src/entities'
import { useTranslation } from 'react-i18next'
import { paymentProviderApi, type PaymentProvider } from './api'
import { PaymentProviderForm } from './PaymentProviderForm'
import { useFormModal } from '@pimcore/studio-ui-bundle/components'

export const PaymentProviderManager: React.FC = () => {
    const { t } = useTranslation()
    const modal = useFormModal()

    return (
        <EntityTabbedManager<PaymentProvider>
            api={paymentProviderApi}
            dragType="coreshop:payment_provider"
            localizable
            leftRootTitle={t('coreshop_payment_providers', { defaultValue: 'Payment Providers' })}
            getTitle={(li, data) => data?.identifier ?? (li as any)?.identifier ?? li?.name ?? `Provider #${data?.id ?? li?.id ?? ''}`}
            buildSavePayload={(data) => data}
            onAdd={async () => await new Promise<number>((resolve) => {
                modal.input({
                    title: t('coreshop_payment_provider_add', { defaultValue: 'Add Payment Provider' }),
                    label: t('coreshop_identifier', { defaultValue: 'Identifier' }),
                    rule: { required: true, message: t('coreshop_identifier_required', { defaultValue: 'Identifier is required' }) },
                    onOk: async (value: string) => {
                        // Backend expects 'name' for validation, but PaymentProvider uses 'identifier'
                        const res = await paymentProviderApi.add({ name: value, identifier: value })
                        if (res.data.id !== undefined) {
                            resolve(res.data.id)
                        }
                    }
                })
            })}
            renderDetail={(data, setData, ctx) => {
                if (!data) {
                    return <div style={{ padding: 12, color: 'var(--ant-color-text-tertiary)' }}>
                        {t('coreshop_payment_provider_select', { defaultValue: 'Select a payment provider to view details.' })}
                    </div>
                }

                return (
                    <PaymentProviderForm data={data} onChange={setData} currentLocale={ctx?.currentLocale ?? 'en'} />
                )
            }}
        />
    )
}
