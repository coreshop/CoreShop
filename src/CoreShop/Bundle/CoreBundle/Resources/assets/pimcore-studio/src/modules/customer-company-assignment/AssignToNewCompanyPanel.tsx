/**
 * CoreShop Customer to New Company Assignment Panel
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Button, Form, Spin, Result } from 'antd'
import { CheckOutlined, UserOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { container } from '@pimcore/studio-ui-bundle'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { AssignmentForm } from './AssignmentForm'
import { customerCompanyApi, type EntityDetails, type ValidationData, type DuplicateCompany } from './api'
import { getErrorMessage } from '@coreshop/resource/src/entities'

const useStyles = createStyles(({ css }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 24px;
  `,
  header: css`
    margin-bottom: 24px;
  `,
  content: css`
    flex: 1;
    overflow: auto;
  `,
  selectButton: css`
    margin-top: 16px;
  `,
  centered: css`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  `
}))

type Step = 'select-customer' | 'form' | 'success'

export const AssignToNewCompanyPanel: React.FC = () => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()
  const [form] = Form.useForm()

  const [step, setStep] = React.useState<Step>('select-customer')
  const [loading, setLoading] = React.useState(false)
  const [submitting, setSubmitting] = React.useState(false)

  const [customerId, setCustomerId] = React.useState<number | null>(null)
  const [customerData, setCustomerData] = React.useState<EntityDetails | null>(null)
  const [validationData, setValidationData] = React.useState<ValidationData | null>(null)

  const [companyName, setCompanyName] = React.useState('')
  const [duplicates, setDuplicates] = React.useState<DuplicateCompany[]>([])
  const [showDuplicates, setShowDuplicates] = React.useState(false)
  const [checkingDuplicates, setCheckingDuplicates] = React.useState(false)

  const [allowedCustomerClasses, setAllowedCustomerClasses] = React.useState<string[]>([])

  const debounceRef = React.useRef<ReturnType<typeof setTimeout> | null>(null)

  // Load allowed customer classes from config
  React.useEffect(() => {
    const loadAllowedClasses = async () => {
      try {
        const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
        const classes = await configProvider.getAllowedClasses('coreshop.customer')
        setAllowedCustomerClasses(classes)
      } catch (err) {
        void messageApi.error(getErrorMessage(err, 'Failed to load allowed customer classes'))
        setAllowedCustomerClasses(['CoreShopCustomer'])
      }
    }
    void loadAllowedClasses()
  }, [])

  const handleCustomerSelected = React.useCallback(async (id: number) => {
    setLoading(true)
    try {
      const response = await customerCompanyApi.getEntityDetails('customer', id)
      if (response.success && response.data) {
        setCustomerId(id)
        setCustomerData(response.data)
        await validateAssignment(id)
      } else {
        void messageApi.error(response.message ?? 'Failed to load customer details')
      }
    } catch (error) {
      void messageApi.error('Failed to load customer details')
    } finally {
      setLoading(false)
    }
  }, [])

  const { open: openCustomerSelector } = useElementSelector({
    selectionType: SelectionType.Single,
    areas: {
      asset: false,
      document: false,
      object: true
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses: allowedCustomerClasses.length > 0 ? allowedCustomerClasses : undefined
      }
    },
    onFinish: (event) => {
      if (event.items.length > 0) {
        const selected = event.items[0]
        void handleCustomerSelected(selected.data.id)
      }
    }
  })

  const validateAssignment = async (custId: number): Promise<void> => {
    setLoading(true)
    try {
      const response = await customerCompanyApi.validateAssignment(custId)
      if (response.success && response.data) {
        setValidationData(response.data)
        setStep('form')
      } else {
        void messageApi.error(response.message ?? 'Customer cannot be assigned to a company')
        resetState()
      }
    } catch (error) {
      void messageApi.error('Failed to validate assignment')
      resetState()
    } finally {
      setLoading(false)
    }
  }

  const handleCompanyNameChange = (name: string): void => {
    setCompanyName(name)

    if (debounceRef.current) {
      clearTimeout(debounceRef.current)
    }

    if (name.length < 3) {
      setShowDuplicates(false)
      setDuplicates([])
      return
    }

    debounceRef.current = setTimeout(async () => {
      setCheckingDuplicates(true)
      try {
        const response = await customerCompanyApi.checkDuplicateNames(name)
        if (response.list && response.list.length > 0) {
          setDuplicates(response.list)
          setShowDuplicates(true)
        } else {
          setDuplicates([])
          setShowDuplicates(false)
        }
      } catch (error) {
        void messageApi.error(getErrorMessage(error, 'Failed to check duplicates'))
      } finally {
        setCheckingDuplicates(false)
      }
    }, 500)
  }

  const handleSubmit = async (): Promise<void> => {
    try {
      const values = await form.validateFields()
      if (!customerId) return

      setSubmitting(true)

      const response = await customerCompanyApi.dispatchNewAssignment(customerId, {
        addressAssignmentType: values.addressAssignmentType,
        addressAccessType: values.addressAccessType,
        newCompanyName: values.newCompanyName,
      })

      if (response.success) {
        void messageApi.success(
          t('coreshop_customer_transformer_assignment_form_success', {
            defaultValue: 'Customer successfully assigned to company',
          })
        )
        setStep('success')

        // Refresh Pimcore tree and open objects
        const pimcore = (window as any).pimcore
        if (pimcore) {
          pimcore.elementservice?.refreshRootNodeAllTrees?.('object')
          if (response.customerId) {
            pimcore.helpers?.openObject?.(response.customerId, 'object')
          }
          if (response.companyId) {
            pimcore.helpers?.openObject?.(response.companyId, 'object')
          }
        }
      } else {
        void messageApi.error(response.message ?? 'Failed to assign customer to company')
      }
    } catch (error) {
      if ((error as any)?.errorFields) {
        // Form validation error, ignore
        return
      }
      void messageApi.error('Failed to submit assignment')
    } finally {
      setSubmitting(false)
    }
  }

  const resetState = (): void => {
    setStep('select-customer')
    setCustomerId(null)
    setCustomerData(null)
    setValidationData(null)
    setCompanyName('')
    setDuplicates([])
    setShowDuplicates(false)
    form.resetFields()
  }

  if (loading) {
    return (
      <div className={styles.centered}>
        <Spin size="large" />
      </div>
    )
  }

  if (step === 'success') {
    return (
      <div className={styles.container}>
        <Result
          status="success"
          title={t('coreshop_customer_transformer_assignment_form_success', {
            defaultValue: 'Customer successfully assigned to company',
          })}
          extra={
            <Button type="primary" onClick={resetState}>
              {t('assign_another', { defaultValue: 'Assign Another Customer' })}
            </Button>
          }
        />
      </div>
    )
  }

  if (step === 'select-customer') {
    return (
      <div className={styles.container}>
        <Result
          icon={<UserOutlined />}
          title={t('coreshop_customer_transformer_select_customer', {
            defaultValue: 'Select a Customer',
          })}
          subTitle={t('coreshop_customer_transformer_select_customer_description', {
            defaultValue: 'Please select a customer to assign to a new company.',
          })}
          extra={
            <Button type="primary" icon={<UserOutlined />} onClick={openCustomerSelector}>
              {t('select_customer', { defaultValue: 'Select Customer' })}
            </Button>
          }
        />
      </div>
    )
  }

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h2>
          {t('coreshop_customer_transformer_assignment_form_title', {
            defaultValue: 'Assignment Overview',
          })}
        </h2>
      </div>

      <div className={styles.content}>
        <Form form={form} layout="vertical">
          <AssignmentForm
            customerId={customerId!}
            customerName={customerData?.name ?? ''}
            addresses={validationData?.addresses ?? []}
            isNewCompany={true}
            onCompanyNameChange={handleCompanyNameChange}
            duplicates={duplicates}
            showDuplicates={showDuplicates}
          />

          <Form.Item>
            <Button
              type="primary"
              icon={<CheckOutlined />}
              onClick={handleSubmit}
              loading={submitting || checkingDuplicates}
            >
              {t('coreshop_customer_transformer_assignment_new_form_button', {
                defaultValue: 'Create Company and assign Customer',
              })}
            </Button>
          </Form.Item>
        </Form>
      </div>
    </div>
  )
}
