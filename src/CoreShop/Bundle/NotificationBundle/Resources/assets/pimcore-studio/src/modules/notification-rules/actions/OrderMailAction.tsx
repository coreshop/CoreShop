/**
 * CoreShop NotificationBundle Studio Plugin
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
import { Form, Checkbox, Tabs, Typography, Divider } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ActionComponentProps } from '@coreshop/rule/src/rules/types'
import { DocumentSelect } from '@coreshop/pimcore/src/components/DocumentSelect'

interface OrderMailActionConfig {
  mails?: Record<string, number | null>
  doNotSendToDesignatedRecipient?: boolean
  sendInvoices?: boolean
  sendShipments?: boolean
}

// Get available languages from Pimcore
const getAvailableLanguages = (): string[] => {
  try {
    // @ts-ignore - Pimcore global
    return window.pimcore?.settings?.websiteLanguages ?? ['en']
  } catch {
    return ['en']
  }
}

export const OrderMailAction: React.FC<ActionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const config = (data ?? {}) as OrderMailActionConfig
  const languages = getAvailableLanguages()

  const handleMailChange = (lang: string, documentId: number | null) => {
    onChange({
      ...config,
      mails: {
        ...(config.mails ?? {}),
        [lang]: documentId
      }
    })
  }

  const handleConfigChange = (key: keyof OrderMailActionConfig, value: boolean) => {
    onChange({
      ...config,
      [key]: value
    })
  }

  const tabItems = languages.map(lang => ({
    key: lang,
    label: lang.toUpperCase(),
    children: (
      <div style={{ padding: 16 }}>
        <Form.Item
          label={t('coreshop_email_document', { defaultValue: 'Email Document' })}
        >
          <DocumentSelect
            value={config.mails?.[lang] ?? null}
            onChange={(id) => handleMailChange(lang, id)}
            documentTypes={['email']}
            placeholder={t('coreshop_select_email_document', { defaultValue: 'Select an email document' })}
          />
        </Form.Item>
      </div>
    )
  }))

  return (
    <>
      <Typography.Text type="secondary" style={{ display: 'block', marginBottom: 16 }}>
        {t('coreshop_order_mail_action_description', {
          defaultValue: 'Configure email documents for each language. You can optionally attach invoices and shipments to the email.'
        })}
      </Typography.Text>

      <Tabs
        defaultActiveKey={languages[0]}
        items={tabItems}
        size="small"
      />

      <Divider style={{ margin: '16px 0' }} />

      <Typography.Text strong style={{ display: 'block', marginBottom: 12 }}>
        {t('coreshop_mail_attachments', { defaultValue: 'Attachments' })}
      </Typography.Text>

      <Form.Item style={{ marginBottom: 8 }}>
        <Checkbox
          checked={config.sendInvoices ?? false}
          onChange={(e) => handleConfigChange('sendInvoices', e.target.checked)}
        >
          {t('coreshop_mail_rule_send_invoices', {
            defaultValue: 'Attach invoices to email'
          })}
        </Checkbox>
      </Form.Item>

      <Form.Item style={{ marginBottom: 8 }}>
        <Checkbox
          checked={config.sendShipments ?? false}
          onChange={(e) => handleConfigChange('sendShipments', e.target.checked)}
        >
          {t('coreshop_mail_rule_send_shipments', {
            defaultValue: 'Attach shipments to email'
          })}
        </Checkbox>
      </Form.Item>

      <Divider style={{ margin: '16px 0' }} />

      <Form.Item>
        <Checkbox
          checked={config.doNotSendToDesignatedRecipient ?? false}
          onChange={(e) => handleConfigChange('doNotSendToDesignatedRecipient', e.target.checked)}
        >
          {t('coreshop_mail_rule_do_not_send_to_designated_recipient', {
            defaultValue: 'Do not send to designated recipient'
          })}
        </Checkbox>
      </Form.Item>
    </>
  )
}
