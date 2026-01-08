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
import { Input, InputNumber, Checkbox, Tabs, Typography, Divider, Tag } from 'antd'
import { GlobalOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { AssetSelect } from '@coreshop/pimcore/src/components/AssetSelect'
import type { PaymentProvider, GatewayConfig, PaymentProviderRuleGroup } from './api'
import { GatewayConfigPanel } from './gateways'
import { PaymentProviderRuleGroupPanel } from './PaymentProviderRuleGroupPanel'

// Helper to render localized field labels with visual indicator
const LocalizedLabel: React.FC<{ label: string, locale: string }> = ({ label, locale }) => (
    <span style={{ display: 'flex', alignItems: 'center', gap: 6 }}>
        <GlobalOutlined style={{ color: 'var(--ant-color-primary)', fontSize: 12 }} />
        {label}
        <Tag color="blue" style={{ marginLeft: 4, fontSize: 10, lineHeight: '16px', padding: '0 4px' }}>
            {locale.toUpperCase()}
        </Tag>
    </span>
)

// Simple form field wrapper
const FormField: React.FC<{
    label: React.ReactNode
    required?: boolean
    children: React.ReactNode
}> = ({ label, required, children }) => (
    <div style={{ marginBottom: 16 }}>
        <label style={{ display: 'block', marginBottom: 4, fontWeight: 500 }}>
            {label}
            {required && <span style={{ color: 'var(--ant-color-error)', marginLeft: 4 }}>*</span>}
        </label>
        {children}
    </div>
)

export const PaymentProviderForm: React.FC<{
    data?: PaymentProvider
    onChange: (draft: Partial<PaymentProvider>) => void
    currentLocale: string
}> = ({ data, onChange, currentLocale }) => {
    const { t } = useTranslation()

    // Helper to get translation value
    const getTranslation = (field: 'title' | 'description' | 'instructions'): string => {
        return data?.translations?.[currentLocale]?.[field] ?? ''
    }

    // Helper to set translation value
    const setTranslation = (field: 'title' | 'description' | 'instructions', value: string) => {
        const currentTranslations = data?.translations ?? {}
        const currentLocaleTranslations = currentTranslations[currentLocale] ?? {}

        onChange({
            translations: {
                ...currentTranslations,
                [currentLocale]: {
                    ...currentLocaleTranslations,
                    [field]: value
                }
            }
        })
    }

    // Helper to update a simple field
    const updateField = <K extends keyof PaymentProvider>(field: K, value: PaymentProvider[K]) => {
        onChange({ [field]: value } as Partial<PaymentProvider>)
    }

    const settingsTab = (
        <div style={{ padding: 24 }}>
            <FormField label={t('coreshop_identifier', { defaultValue: 'Identifier' })} required>
                <Input
                    value={data?.identifier ?? ''}
                    onChange={(e) => updateField('identifier', e.target.value)}
                />
            </FormField>

            <FormField label={t('coreshop_position', { defaultValue: 'Position' })}>
                <InputNumber
                    style={{ width: '100%' }}
                    value={data?.position}
                    onChange={(value) => updateField('position', value ?? undefined)}
                />
            </FormField>

            <FormField label="">
                <Checkbox
                    checked={data?.active ?? false}
                    onChange={(e) => updateField('active', e.target.checked)}
                >
                    {t('coreshop_active', { defaultValue: 'Active' })}
                </Checkbox>
            </FormField>

            <FormField label={t('coreshop_logo', { defaultValue: 'Logo' })}>
                <AssetSelect
                    accept={['asset', 'asset:image']}
                    value={data?.logo}
                    onChange={(value) => updateField('logo', value)}
                />
            </FormField>

            {/* Translations */}
            <FormField label={<LocalizedLabel label={t('coreshop_title', { defaultValue: 'Title' })} locale={currentLocale} />}>
                <Input
                    value={getTranslation('title')}
                    onChange={(e) => setTranslation('title', e.target.value)}
                />
            </FormField>

            <FormField label={<LocalizedLabel label={t('coreshop_description', { defaultValue: 'Description' })} locale={currentLocale} />}>
                <Input.TextArea
                    rows={3}
                    value={getTranslation('description')}
                    onChange={(e) => setTranslation('description', e.target.value)}
                />
            </FormField>

            <FormField label={<LocalizedLabel label={t('coreshop_instructions', { defaultValue: 'Instructions' })} locale={currentLocale} />}>
                <Input.TextArea
                    rows={3}
                    value={getTranslation('instructions')}
                    onChange={(e) => setTranslation('instructions', e.target.value)}
                />
            </FormField>

            {/* Gateway Configuration */}
            <Divider orientation="left">
                {t('coreshop_gateway_configuration', { defaultValue: 'Gateway Configuration' })}
            </Divider>

            <GatewayConfigPanel
                gatewayConfig={data?.gatewayConfig}
                onChange={(gatewayConfig: GatewayConfig) => {
                    onChange({ gatewayConfig })
                }}
            />
        </div>
    )

    const rulesTab = (
        <div style={{ padding: 24 }}>
            <Typography.Title level={5} style={{ marginBottom: 16 }}>
                {t('coreshop_payment_provider_rules', { defaultValue: 'Payment Provider Rules' })}
            </Typography.Title>
            <Typography.Text type="secondary" style={{ display: 'block', marginBottom: 16 }}>
                {t('coreshop_payment_provider_rules_description', {
                    defaultValue: 'Assign payment provider rules to control pricing and availability. Rules are evaluated in priority order.'
                })}
            </Typography.Text>
            <PaymentProviderRuleGroupPanel
                ruleGroups={data?.paymentProviderRules ?? []}
                onChange={(ruleGroups: PaymentProviderRuleGroup[]) => {
                    onChange({ paymentProviderRules: ruleGroups })
                }}
            />
        </div>
    )

    return (
        <Tabs
            defaultActiveKey="settings"
            items={[
                {
                    key: 'settings',
                    label: t('coreshop_settings', { defaultValue: 'Settings' }),
                    children: settingsTab
                },
                {
                    key: 'rules',
                    label: t('coreshop_payment_provider_rules', { defaultValue: 'Payment Provider Rules' }),
                    children: rulesTab
                }
            ]}
            style={{ flex: 1, overflow: 'auto' }}
            tabBarStyle={{ paddingLeft: 24, paddingRight: 24, marginBottom: 0 }}
        />
    )
}
