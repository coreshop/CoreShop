/**
 * CoreShop Customer to Existing Company Assignment Panel
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Button, Spin, Result, Alert } from 'antd'
import { CheckOutlined, UserOutlined, BankOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { container } from '@pimcore/studio-ui-bundle'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { AssignmentForm, type AssignmentFormValues } from './AssignmentForm'
import { customerCompanyApi, type EntityDetails, type ValidationData } from './api'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

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
  centered: css`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
  `,
  selectionInfo: css`
    margin-bottom: 16px;
  `
}))

type Step = 'select-customer' | 'select-company' | 'form' | 'success'

const defaultFormData: AssignmentFormValues = {
  addressAssignmentType: '',
  addressAccessType: 'own_only',
}

export const AssignToExistingCompanyPanel: React.FC = () => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()

  const [step, setStep] = React.useState<Step>('select-customer')
  const [loading, setLoading] = React.useState(false)
  const [submitting, setSubmitting] = React.useState(false)

  const [customerId, setCustomerId] = React.useState<number | null>(null)
  const [customerData, setCustomerData] = React.useState<EntityDetails | null>(null)
  const [companyId, setCompanyId] = React.useState<number | null>(null)
  const [companyData, setCompanyData] = React.useState<EntityDetails | null>(null)
  const [validationData, setValidationData] = React.useState<ValidationData | null>(null)

  const [formData, setFormData] = React.useState<AssignmentFormValues>({ ...defaultFormData })

  const [allowedCustomerClasses, setAllowedCustomerClasses] = React.useState<string[]>([])
  const [allowedCompanyClasses, setAllowedCompanyClasses] = React.useState<string[]>([])

  // Load allowed classes from config
  React.useEffect(() => {
    const loadAllowedClasses = async () => {
      try {
        const configProvider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
        const customerClasses = await configProvider.getAllowedClasses('coreshop.customer')
        const companyClasses = await configProvider.getAllowedClasses('coreshop.company')
        setAllowedCustomerClasses(customerClasses)
        setAllowedCompanyClasses(companyClasses)
      } catch (err) {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load allowed classes')))
        setAllowedCustomerClasses(['CoreShopCustomer'])
        setAllowedCompanyClasses(['CoreShopCompany'])
      }
    }
    void loadAllowedClasses()
  }, [])

  // Store customerId in a ref so the company selector callback can access it
  const customerIdRef = React.useRef<number | null>(null)
  React.useEffect(() => {
    customerIdRef.current = customerId
  }, [customerId])

  const handleCustomerSelected = React.useCallback(async (id: number) => {
    setLoading(true)
    try {
      const response = await customerCompanyApi.getEntityDetails('customer', id)
      if (response.success && response.data) {
        setCustomerId(id)
        setCustomerData(response.data)
        const translatedMessage = t('coreshop_customer_transformer_assignment_selected_customer', {
          name: response.data.name,
          id: id,
          defaultValue: `Selected customer: <strong>${response.data.name}</strong>. Id: ${id}. Click "OK" to now select a company.`,
        })
        void messageApi.success(
          <span dangerouslySetInnerHTML={{ __html: translatedMessage }} />
        )
        setStep('select-company')
      } else {
        void messageApi.error(renderApiError(response.message ?? 'Failed to load customer details'))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load customer details')))
    } finally {
      setLoading(false)
    }
  }, [t])

  const handleCompanySelected = React.useCallback(async (id: number) => {
    setLoading(true)
    try {
      const response = await customerCompanyApi.getEntityDetails('company', id)
      if (response.success && response.data) {
        setCompanyId(id)
        setCompanyData(response.data)
        // Validate assignment with the current customerId
        const custId = customerIdRef.current
        if (custId) {
          await validateAssignment(custId, id)
        }
      } else {
        void messageApi.error(renderApiError(response.message ?? 'Failed to load company details'))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load company details')))
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

  const { open: openCompanySelector } = useElementSelector({
    selectionType: SelectionType.Single,
    areas: {
      asset: false,
      document: false,
      object: true
    },
    config: {
      objects: {
        allowedTypes: ['object'],
        allowedClasses: allowedCompanyClasses.length > 0 ? allowedCompanyClasses : undefined
      }
    },
    onFinish: (event) => {
      if (event.items.length > 0) {
        const selected = event.items[0]
        void handleCompanySelected(selected.data.id)
      }
    }
  })

  const validateAssignment = async (custId: number, compId: number): Promise<void> => {
    setLoading(true)
    try {
      const response = await customerCompanyApi.validateAssignment(custId, compId)
      if (response.success && response.data) {
        setValidationData(response.data)
        const hasAddresses = response.data.addresses.length > 0
        setFormData({
          ...defaultFormData,
          addressAssignmentType: hasAddresses ? '' : 'keep',
        })
        setStep('form')
      } else {
        void messageApi.error(renderApiError(response.message ?? 'Customer cannot be assigned to this company'))
        resetState()
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to validate assignment')))
      resetState()
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (): Promise<void> => {
    if (!customerId || !companyId) return
    if (!formData.addressAssignmentType) {
      void messageApi.error(renderApiError(t('field_required', { defaultValue: 'This field is required' })))
      return
    }

    setSubmitting(true)
    try {
      const response = await customerCompanyApi.dispatchExistingAssignment(customerId, companyId, {
        addressAssignmentType: formData.addressAssignmentType,
        addressAccessType: formData.addressAccessType,
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
        void messageApi.error(renderApiError(response.message ?? 'Failed to assign customer to company'))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to submit assignment')))
    } finally {
      setSubmitting(false)
    }
  }

  const resetState = (): void => {
    setStep('select-customer')
    setCustomerId(null)
    setCustomerData(null)
    setCompanyId(null)
    setCompanyData(null)
    setValidationData(null)
    setFormData({ ...defaultFormData })
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
          subTitle={t('coreshop_customer_transformer_select_customer_existing_description', {
            defaultValue: 'Please select a customer to assign to an existing company.',
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

  if (step === 'select-company') {
    return (
      <div className={styles.container}>
        <Alert
          type="success"
          message={t('customer_selected', { defaultValue: 'Customer Selected' })}
          description={`${customerData?.name} (ID: ${customerId})`}
          style={{ marginBottom: 24 }}
        />

        <Result
          icon={<BankOutlined />}
          title={t('coreshop_customer_transformer_select_company', {
            defaultValue: 'Select a Company',
          })}
          subTitle={t('coreshop_customer_transformer_select_company_description', {
            defaultValue: 'Please select the company to assign the customer to.',
          })}
          extra={
            <Button type="primary" icon={<BankOutlined />} onClick={openCompanySelector}>
              {t('select_company', { defaultValue: 'Select Company' })}
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
        <AssignmentForm
          customerId={customerId!}
          customerName={customerData?.name ?? ''}
          companyId={companyId!}
          companyName={companyData?.name ?? ''}
          addresses={validationData?.addresses ?? []}
          isNewCompany={false}
          formData={formData}
          onFormChange={setFormData}
        />

        <Button
          type="primary"
          icon={<CheckOutlined />}
          onClick={handleSubmit}
          loading={submitting}
          style={{ marginTop: 16 }}
        >
          {t('coreshop_customer_transformer_assignment_form_button', {
            defaultValue: 'Assign Customer to Company',
          })}
        </Button>
      </div>
    </div>
  )
}
