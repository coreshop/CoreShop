/**
 * CoreShop Customer to New Company Assignment Modal
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Modal, Button, Spin, Result } from 'antd'
import { CheckOutlined, UserOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { container } from '@pimcore/studio-ui-bundle'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { AssignmentForm, type AssignmentFormValues } from './AssignmentForm'
import { customerCompanyApi, type EntityDetails, type ValidationData, type DuplicateCompany } from './api'
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

type Step = 'select-customer' | 'form' | 'success'

const defaultFormData: AssignmentFormValues = {
  addressAssignmentType: '',
  addressAccessType: 'own_only',
  newCompanyName: '',
}

export interface AssignToNewCompanyModalProps {
  open: boolean
  customerId?: number
  onSuccess?: () => void
  onCancel: () => void
}

export const AssignToNewCompanyModal: React.FC<AssignToNewCompanyModalProps> = ({
  open,
  customerId: initialCustomerId,
  onSuccess,
  onCancel
}) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()

  const [step, setStep] = React.useState<Step>(initialCustomerId ? 'form' : 'select-customer')
  const [loading, setLoading] = React.useState(false)
  const [submitting, setSubmitting] = React.useState(false)

  const [customerId, setCustomerId] = React.useState<number | null>(initialCustomerId ?? null)
  const [customerData, setCustomerData] = React.useState<EntityDetails | null>(null)
  const [validationData, setValidationData] = React.useState<ValidationData | null>(null)

  const [formData, setFormData] = React.useState<AssignmentFormValues>({ ...defaultFormData })
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
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load allowed customer classes')))
        setAllowedCustomerClasses(['CoreShopCustomer'])
      }
    }
    void loadAllowedClasses()
  }, [])

  // Load customer details if initialCustomerId is provided
  React.useEffect(() => {
    if (open && initialCustomerId && !customerData) {
      void handleCustomerSelected(initialCustomerId)
    }
  }, [open, initialCustomerId])

  // Track if we've been open before to avoid resetting during re-renders
  const hasBeenOpenRef = React.useRef(false)
  const previousOpenRef = React.useRef(open)

  React.useEffect(() => {
    // Only reset when transitioning from open to closed (not on initial mount or re-renders)
    if (previousOpenRef.current === true && open === false) {
      setStep(initialCustomerId ? 'form' : 'select-customer')
      setCustomerId(initialCustomerId ?? null)
      setCustomerData(null)
      setValidationData(null)
      setFormData({ ...defaultFormData })
      setDuplicates([])
      setShowDuplicates(false)
      hasBeenOpenRef.current = false
    }
    if (open) {
      hasBeenOpenRef.current = true
    }
    previousOpenRef.current = open
  }, [open, initialCustomerId])

  const handleCustomerSelected = React.useCallback(async (id: number) => {
    setLoading(true)
    try {
      const response = await customerCompanyApi.getEntityDetails('customer', id)
      if (response.success && response.data) {
        setCustomerId(id)
        setCustomerData(response.data)
        await validateAssignment(id)
      } else {
        void messageApi.error(renderApiError(response.message ?? 'Failed to load customer details'))
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load customer details')))
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
        const hasAddresses = response.data.addresses.length > 0
        setFormData({
          ...defaultFormData,
          addressAssignmentType: hasAddresses ? '' : 'keep',
        })
        setStep('form')
      } else {
        void messageApi.error(renderApiError(response.message ?? 'Customer cannot be assigned to a company'))
        resetState()
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to validate assignment')))
      resetState()
    } finally {
      setLoading(false)
    }
  }

  const handleCompanyNameChange = (name: string): void => {
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
        void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to check duplicates')))
      } finally {
        setCheckingDuplicates(false)
      }
    }, 500)
  }

  const handleSubmit = async (): Promise<void> => {
    if (!customerId) return
    if (!formData.newCompanyName) {
      void messageApi.error(renderApiError(t('field_required', { defaultValue: 'This field is required' })))
      return
    }
    if (!formData.addressAssignmentType) {
      void messageApi.error(renderApiError(t('field_required', { defaultValue: 'This field is required' })))
      return
    }

    setSubmitting(true)
    try {
      const response = await customerCompanyApi.dispatchNewAssignment(customerId, {
        addressAssignmentType: formData.addressAssignmentType,
        addressAccessType: formData.addressAccessType,
        newCompanyName: formData.newCompanyName,
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
    setValidationData(null)
    setFormData({ ...defaultFormData })
    setDuplicates([])
    setShowDuplicates(false)
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
          subTitle={t('coreshop_customer_transformer_select_customer_description', {
            defaultValue: 'Please select a customer to assign to a new company.',
          })}
          extra={
            <Button type="primary" icon={<UserOutlined />} onClick={openCustomerSelector}>
              {t('select_customer', { defaultValue: 'Select Customer' })}
            </Button>
          }
        />
      )
    }

    return (
      <div className={styles.content}>
        <AssignmentForm
          customerId={customerId!}
          customerName={customerData?.name ?? ''}
          addresses={validationData?.addresses ?? []}
          isNewCompany={true}
          formData={formData}
          onFormChange={setFormData}
          onCompanyNameChange={handleCompanyNameChange}
          duplicates={duplicates}
          showDuplicates={showDuplicates}
        />
      </div>
    )
  }

  return (
    <Modal
      title={t('coreshop_customer_transformer_assign_to_new_company', {
        defaultValue: 'Assign Customer to New Company',
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
              loading={submitting || checkingDuplicates}
            >
              {t('coreshop_customer_transformer_assignment_new_form_button', {
                defaultValue: 'Create Company and assign Customer',
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
