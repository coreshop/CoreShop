/**
 * CoreShop Customer Company Assignment Form
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Input, Table, Alert, Form } from 'antd'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import { removeFieldDecorator } from '@coreshop/studio-form/src/form-builder/decorators'

const useStyles = createStyles(({ css }) => ({
  form: css`
    .ant-form-item {
      margin-bottom: 16px;
    }
  `,
  fieldset: css`
    border: 1px solid #d9d9d9;
    padding: 16px;
    margin-bottom: 16px;
    border-radius: 4px;
  `,
  fieldsetTitle: css`
    font-weight: 600;
    margin-bottom: 12px;
  `,
  addressTable: css`
    margin-top: 16px;
  `
}))

export interface AssignmentFormValues {
  addressAssignmentType: string
  addressAccessType: string
  newCompanyName?: string
}

export interface AssignmentFormProps {
  customerId: number
  customerName: string
  companyId?: number
  companyName?: string
  addresses: Array<{ id: number; path: string }>
  isNewCompany: boolean
  formData: AssignmentFormValues
  onFormChange: (data: AssignmentFormValues) => void
  onCompanyNameChange?: (name: string) => void
  duplicates?: Array<{ id: number; name: string; path: string }>
  showDuplicates?: boolean
}

export const AssignmentForm: React.FC<AssignmentFormProps> = ({
  customerId,
  customerName,
  companyId,
  companyName,
  addresses,
  isNewCompany,
  formData,
  onFormChange,
  onCompanyNameChange,
  duplicates = [],
  showDuplicates = false,
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()

  const addressColumns = [
    {
      title: t('id', { defaultValue: 'ID' }),
      dataIndex: 'id',
      key: 'id',
      width: 100,
    },
    {
      title: t('path', { defaultValue: 'Path' }),
      dataIndex: 'path',
      key: 'path',
    },
  ]

  const duplicateColumns = [
    {
      title: t('id', { defaultValue: 'ID' }),
      dataIndex: 'id',
      key: 'id',
      width: 80,
    },
    {
      title: t('name', { defaultValue: 'Name' }),
      dataIndex: 'name',
      key: 'name',
    },
    {
      title: t('path', { defaultValue: 'Path' }),
      dataIndex: 'path',
      key: 'path',
    },
  ]

  const decorators = isNewCompany
    ? undefined
    : [{ name: 'hide-company-name', decorator: removeFieldDecorator('newCompanyName') }]

  const handleChange = (draft: Partial<AssignmentFormValues>): void => {
    const updated = { ...formData, ...draft }
    onFormChange(updated)

    if (isNewCompany && 'newCompanyName' in draft) {
      onCompanyNameChange?.(draft.newCompanyName ?? '')
    }
  }

  return (
    <div className={styles.form}>
      <Alert
        type="info"
        message={
          isNewCompany
            ? t('coreshop_customer_transformer_assignment_new_form_description', {
                defaultValue: 'Once this form is submitted, a new company will be created. Please check the data carefully. In addition, you must define whether the customer addresses are to be retained by the customer or transferred to the new company.',
              })
            : t('coreshop_customer_transformer_assignment_form_description', {
                defaultValue: 'Please check the data carefully. You must define whether the customer addresses are to be retained by the customer or transferred to the company.',
              })
        }
        style={{ marginBottom: 16 }}
      />

      <Form layout="vertical">
        <Form.Item
          label={t('coreshop_customer_transformer_assignment_form_customer_id', { defaultValue: 'Customer Id' })}
        >
          <Input value={customerId} disabled />
        </Form.Item>

        <Form.Item
          label={t('coreshop_customer_transformer_assignment_form_customer_name', { defaultValue: 'Customer Name' })}
        >
          <Input value={customerName} disabled />
        </Form.Item>
      </Form>

      {isNewCompany && showDuplicates && duplicates.length > 0 && (
        <Alert
          type="warning"
          message={t('coreshop_customer_transformer_assignment_new_form_maybe_duplicates_title', {
            defaultValue: 'Possible Duplicates Found',
          })}
          description={
            <Table
              dataSource={duplicates}
              columns={duplicateColumns}
              rowKey="id"
              size="small"
              pagination={false}
              style={{ marginTop: 8 }}
            />
          }
          style={{ marginBottom: 16 }}
        />
      )}

      {!isNewCompany && (
        <Form layout="vertical">
          <Form.Item
            label={t('coreshop_customer_transformer_assignment_form_company_id', { defaultValue: 'Company Id' })}
          >
            <Input value={companyId} disabled />
          </Form.Item>

          <Form.Item
            label={t('coreshop_customer_transformer_assignment_form_company_name', { defaultValue: 'Company Name' })}
          >
            <Input value={companyName} disabled />
          </Form.Item>
        </Form>
      )}

      <SchemaForm<AssignmentFormValues>
        blockPrefix="coreshop_customer_company_assignment"
        data={formData}
        onChange={handleChange}
        decorators={decorators}
        embedded
      />

      <div className={styles.addressTable}>
        <Table
          title={() =>
            t('coreshop_customer_transformer_assignment_form_available_customer_addresses', {
              defaultValue: 'Available Addresses (Customer)',
            })
          }
          dataSource={addresses}
          columns={addressColumns}
          rowKey="id"
          size="small"
          pagination={false}
          locale={{
            emptyText: t('no_addresses', { defaultValue: 'No addresses available' }),
          }}
        />
      </div>
    </div>
  )
}
