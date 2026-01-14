/**
 * CoreShop Customer Company Assignment Form
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Form, Input, Select, Table, Alert, Space } from 'antd'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'

const useStyles = createStyles(({ css }) => ({
  form: css`
    .ant-form-item {
      margin-bottom: 16px;
    }
  `,
  description: css`
    background: #e6e6e6;
    padding: 10px;
    margin-bottom: 16px;
    border-radius: 4px;
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
  onCompanyNameChange,
  duplicates = [],
  showDuplicates = false,
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()

  const hasAddresses = addresses.length > 0

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

      {isNewCompany ? (
        <div className={styles.fieldset}>
          <div className={styles.fieldsetTitle}>
            {t('coreshop_customer_transformer_assignment_form_new_company_title', { defaultValue: 'New Company Data' })}
          </div>
          <Form.Item
            name="newCompanyName"
            label={t('coreshop_customer_transformer_assignment_form_company_name', { defaultValue: 'Company Name' })}
            rules={[{ required: true, message: t('field_required', { defaultValue: 'This field is required' }) }]}
          >
            <Input
              placeholder={t('enter_company_name', { defaultValue: 'Enter company name' })}
              onChange={(e) => onCompanyNameChange?.(e.target.value)}
            />
          </Form.Item>

          {showDuplicates && duplicates.length > 0 && (
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
              style={{ marginTop: 8 }}
            />
          )}
        </div>
      ) : (
        <>
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
        </>
      )}

      <Form.Item
        name="addressAssignmentType"
        label={t('coreshop_customer_transformer_assignment_form_address_assignment_type', {
          defaultValue: 'Address Assignment',
        })}
        rules={[{ required: true, message: t('field_required', { defaultValue: 'This field is required' }) }]}
        initialValue={!hasAddresses ? 'keep' : undefined}
      >
        <Select
          placeholder={t('select_option', { defaultValue: 'Select an option' })}
          disabled={!hasAddresses}
          options={[
            {
              value: 'keep',
              label: t('coreshop_customer_transformer_assignment_form_assignment_type_keep', {
                defaultValue: 'Retain addresses at customer',
              }),
            },
            {
              value: 'move',
              label: t('coreshop_customer_transformer_assignment_form_assignment_type_move', {
                defaultValue: 'Move Addresses to company object',
              }),
            },
          ]}
        />
      </Form.Item>

      <Form.Item
        name="addressAccessType"
        label={t('coreshop_customer_transformer_assignment_form_assignment_address_access_type', {
          defaultValue: 'Address Access Type',
        })}
        rules={[{ required: true, message: t('field_required', { defaultValue: 'This field is required' }) }]}
        initialValue="own_only"
      >
        <Select
          disabled={!hasAddresses}
          options={[
            {
              value: 'own_only',
              label: t('coreshop_customer_transformer_assignment_form_assignment_address_access_own_only', {
                defaultValue: 'Own Only',
              }),
            },
            {
              value: 'company_only',
              label: t('coreshop_customer_transformer_assignment_form_assignment_address_access_company_only', {
                defaultValue: 'Company Only',
              }),
            },
            {
              value: 'own_and_company',
              label: t('coreshop_customer_transformer_assignment_form_assignment_address_access_own_and_company', {
                defaultValue: 'Own & Company',
              }),
            },
          ]}
        />
      </Form.Item>

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
