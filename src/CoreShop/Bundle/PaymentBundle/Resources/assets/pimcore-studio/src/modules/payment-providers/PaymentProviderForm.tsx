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
import { Form, Input, InputNumber, Checkbox, Tabs, Space, Typography } from 'antd'
import { useTranslation } from 'react-i18next'
import { LocalizedFieldsProvider } from '@coreshop/resource/src/components/localization/localized-fields'
import { AssetSelect } from '@coreshop/pimcore/src/components/AssetSelect'
import type { PaymentProvider } from './api'

export const PaymentProviderForm: React.FC<{
    data?: PaymentProvider
    onChange: (draft: Partial<PaymentProvider>) => void
    currentLocale: string
}> = ({ data, onChange, currentLocale }) => {
    const { t } = useTranslation()
    const [form] = Form.useForm()

    React.useEffect(() => {
        const initial: any = { ...(data ?? {}) }

        // Ensure translations structure
        initial.translations = initial.translations ?? {}
        if (!initial.translations[currentLocale]) {
            initial.translations[currentLocale] = {
                title: '',
                description: '',
                instructions: ''
            }
        }

        form.setFieldsValue(initial)
    }, [data, currentLocale, form])

    const settingsTab = (
        <div style={{ padding: 24 }}>
            <Space align='baseline' style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                <Typography.Text type='secondary'>{t('coreshop_translations', { defaultValue: 'Translations' })}</Typography.Text>
                <Typography.Text type='secondary'>{currentLocale.toUpperCase()}</Typography.Text>
            </Space>

            <Form
                form={form}
                layout='vertical'
                onValuesChange={(_, allValues) => {
                    const mergedTranslations = {
                        ...(data?.translations ?? {}),
                        ...(allValues?.translations ?? {})
                    }
                    onChange({
                        ...allValues,
                        translations: mergedTranslations
                    })
                }}
            >
                <Form.Item
                    label={t('coreshop_identifier', { defaultValue: 'Identifier' })}
                    name='identifier'
                    rules={[{ required: true }]}
                >
                    <Input />
                </Form.Item>

                <Form.Item
                    label={t('coreshop_position', { defaultValue: 'Position' })}
                    name='position'
                >
                    <InputNumber style={{ width: '100%' }} />
                </Form.Item>

                <Form.Item
                    name='active'
                    valuePropName='checked'
                >
                    <Checkbox>
                        {t('coreshop_active', { defaultValue: 'Active' })}
                    </Checkbox>
                </Form.Item>

                <Form.Item
                    label={t('coreshop_logo', { defaultValue: 'Logo' })}
                    name='logo'
                >
                    <AssetSelect accept={['asset', 'asset:image']} />
                </Form.Item>

                {/* Translations */}
                <LocalizedFieldsProvider locales={[currentLocale]}>
                    <Form.Item
                        label={`${t('coreshop_title', { defaultValue: 'Title' })} (${currentLocale.toUpperCase()})`}
                        name={['translations', currentLocale, 'title']}
                    >
                        <Input />
                    </Form.Item>

                    <Form.Item
                        label={`${t('coreshop_description', { defaultValue: 'Description' })} (${currentLocale.toUpperCase()})`}
                        name={['translations', currentLocale, 'description']}
                    >
                        <Input.TextArea rows={3} />
                    </Form.Item>

                    <Form.Item
                        label={`${t('coreshop_instructions', { defaultValue: 'Instructions' })} (${currentLocale.toUpperCase()})`}
                        name={['translations', currentLocale, 'instructions']}
                    >
                        <Input.TextArea rows={3} />
                    </Form.Item>
                </LocalizedFieldsProvider>

                {/* TODO: Gateway Configuration */}
                <div style={{ marginTop: 24, padding: 16, background: '#f5f5f5', borderRadius: 4 }}>
                    <Typography.Text type="secondary">
                        Gateway Configuration (Not implemented yet)
                    </Typography.Text>
                </div>
            </Form>
        </div>
    )

    const rulesTab = (
        <div style={{ padding: 24 }}>
            <Typography.Text type="secondary">
                Payment Provider Rules (Not implemented yet)
            </Typography.Text>
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
