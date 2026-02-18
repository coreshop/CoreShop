/**
 * CoreShop Customer to Existing Company Assignment Modal
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Modal, Button, Spin, Result, Alert } from 'antd'
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
  content: css`
    min-height: 300px;
  `,
  centered: css`
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
  `
}))

type Step = 'select-customer' | 'select-company' | 'form' | 'success'

const defaultFormData: AssignmentFormValues = {
  addressAssignmentType: '',
  addressAccessType: 'own_only',
}

export interface AssignToExistingCompanyModalProps {
  open: boolean
  customerId?: number
  companyId?: number
  onSuccess?: () => void
  onCancel: () => void
}

export const AssignToExistingCompanyModal: React.FC<AssignToExistingCompanyModalProps> = ({
  open,
  customerId: initialCustomerId,
  companyId: initialCompanyId,
  onSuccess,
  onCancel
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()

  const getInitialStep = (): Step => {
    if (initialCustomerId && initialCompanyId) return 'form'
    if (initialCustomerId) return 'select-company'
    return 'select-customer'
  }

  const [step, setStep] = React.useState<Step>(getInitialStep())
  const [loading, setLoading] = React.useState(false)
  const [submitting, setSubmitting] = React.useState(false)

  const [customerId, setCustomerId] = React.useState<number | null>(initialCustomerId ?? null)
  const [customerData, setCustomerData] = React.useState<EntityDetails | null>(null)
  const [companyId, setCompanyId] = React.useState<number | null>(initialCompanyId ?? null)
  const [companyData, setCompanyData] = React.useState<EntityDetails | null>(null)
  const [validationData, setValidationData] = React.useState<ValidationData | null>(null)

  const [formData, setFormData] = React.useState<AssignmentFormValues>({ ...defaultFormData })

  const [allowedCustomerClasses, setAllowedCustomerClasses] = React.useState<string[]>([])
  const [allowedCompanyClasses, setAllowedCompanyClasses] = React.useState<string[]>([])

  // Store customerId in a ref so the company selector callback can access it
  const customerIdRef = React.useRef<number | null>(null)
  React.useEffect(() => {
    customerIdRef.current = customerId
  }, [customerId])

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

  // Load customer (and optionally company) details when modal opens with initial IDs
  React.useEffect(() => {
    if (open && initialCustomerId && !customerData) {
      if (initialCompanyId) {
        // Both provided — load customer, company, and validate in one go
        void (async () => {
          setLoading(true)
          try {
            const [custResponse, compResponse] = await Promise.all([
              customerCompanyApi.getEntityDetails('customer', initialCustomerId),
              customerCompanyApi.getEntityDetails('company', initialCompanyId),
            ])
            if (custResponse.success && custResponse.data) {
              setCustomerId(initialCustomerId)
              setCustomerData(custResponse.data)
            }
            if (compResponse.success && compResponse.data) {
              setCompanyId(initialCompanyId)
              setCompanyData(compResponse.data)
            }
            if (custResponse.success && compResponse.success) {
              await validateAssignment(initialCustomerId, initialCompanyId)
            }
          } catch (error) {
            void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load details')))
          } finally {
            setLoading(false)
          }
        })()
      } else {
        void loadCustomerDetails(initialCustomerId)
      }
    }
  }, [open, initialCustomerId, initialCompanyId])

  // Track previous open state to only reset when actually closing
  const previousOpenRef = React.useRef(open)

  React.useEffect(() => {
    // Only reset when transitioning from open to closed (not on initial mount or re-renders)
    if (previousOpenRef.current === true && open === false) {
      setStep(getInitialStep())
      setCustomerId(initialCustomerId ?? null)
      setCustomerData(null)
      setCompanyId(initialCompanyId ?? null)
      setCompanyData(null)
      setValidationData(null)
      setFormData({ ...defaultFormData })
    }
    previousOpenRef.current = open
  }, [open, initialCustomerId, initialCompanyId])

  const loadCustomerDetails = async (id: number): Promise<void> => {
    setLoading(true)
    try {
      const response = await customerCompanyApi.getEntityDetails('customer', id)
      if (response.success && response.data) {
        setCustomerId(id)
        setCustomerData(response.data)
        setStep('select-company')
      } else {
        void messageApi.error(renderApiError(response.message ?? 'Failed to load customer details'))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load customer details')))
    } finally {
      setLoading(false)
    }
  }

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

        onSuccess?.()
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

  const handleClose = (): void => {
    resetState()
    onCancel()
  }

  const renderContent = () => {
    if (loading) {
      return (
        <div className={styles.centered}>
          <Spin size="large" />
        </div>
      )
    }

    if (step === 'success') {
      return (
        <Result
          status="success"
          title={t('coreshop_customer_transformer_assignment_form_success', {
            defaultValue: 'Customer successfully assigned to company',
          })}
        />
      )
    }

    if (step === 'select-customer') {
      return (
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
      )
    }

    if (step === 'select-company') {
      return (
        <>
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
        </>
      )
    }

    return (
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
      </div>
    )
  }

  return (
    <Modal
      title={t('coreshop_customer_transformer_assign_to_existing_company', {
        defaultValue: 'Assign Customer to Existing Company',
      })}
      open={open}
      onCancel={handleClose}
      width={700}
      footer={
        step === 'form' ? (
          <>
            <Button onClick={handleClose}>
              {t('cancel', { defaultValue: 'Cancel' })}
            </Button>
            <Button
              type="primary"
              icon={<CheckOutlined />}
              onClick={handleSubmit}
              loading={submitting}
            >
              {t('coreshop_customer_transformer_assignment_form_button', {
                defaultValue: 'Assign Customer to Company',
              })}
            </Button>
          </>
        ) : step === 'success' ? (
          <Button type="primary" onClick={handleClose}>
            {t('close', { defaultValue: 'Close' })}
          </Button>
        ) : null
      }
    >
      {renderContent()}
    </Modal>
  )
}
