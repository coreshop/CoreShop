/**
 * CoreShop Customer to New Company Assignment Modal
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Modal, Button, Form, message, Spin, Result } from 'antd'
import { CheckOutlined, UserOutlined } from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { container } from '@pimcore/studio-ui-bundle'
import { useElementSelector, SelectionType } from '@pimcore/studio-ui-bundle/modules/element'
import type { ResourceConfigProvider } from '@coreshop/resource/src/config'
import { coreshopResourceServiceIds } from '@coreshop/resource/src/config'
import { AssignmentForm } from './AssignmentForm'
import { customerCompanyApi, type EntityDetails, type ValidationData, type DuplicateCompany } from './api'

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
  const [form] = Form.useForm()

  const [step, setStep] = React.useState<Step>(initialCustomerId ? 'form' : 'select-customer')
  const [loading, setLoading] = React.useState(false)
  const [submitting, setSubmitting] = React.useState(false)

  const [customerId, setCustomerId] = React.useState<number | null>(initialCustomerId ?? null)
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
        console.error('Failed to load allowed customer classes:', err)
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
      console.log('[AssignToNewCompanyModal] Modal closed, resetting state')
      setStep(initialCustomerId ? 'form' : 'select-customer')
      setCustomerId(initialCustomerId ?? null)
      setCustomerData(null)
      setValidationData(null)
      setCompanyName('')
      setDuplicates([])
      setShowDuplicates(false)
      form.resetFields()
      hasBeenOpenRef.current = false
    }
    if (open) {
      hasBeenOpenRef.current = true
    }
    previousOpenRef.current = open
  }, [open, initialCustomerId, form])

  const handleCustomerSelected = React.useCallback(async (id: number) => {
    console.log('[AssignToNewCompanyModal] handleCustomerSelected called with id:', id)
    setLoading(true)
    try {
      console.log('[AssignToNewCompanyModal] Calling getEntityDetails API...')
      const response = await customerCompanyApi.getEntityDetails('customer', id)
      console.log('[AssignToNewCompanyModal] getEntityDetails response:', response)
      if (response.success && response.data) {
        setCustomerId(id)
        setCustomerData(response.data)
        console.log('[AssignToNewCompanyModal] Calling validateAssignment...')
        await validateAssignment(id)
      } else {
        console.error('[AssignToNewCompanyModal] getEntityDetails failed:', response.message)
        message.error(response.message ?? 'Failed to load customer details')
      }
    } catch (error) {
      console.error('[AssignToNewCompanyModal] Error in handleCustomerSelected:', error)
      message.error('Failed to load customer details')
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
      console.log('[AssignToNewCompanyModal] onFinish called with event:', event)
      if (event.items.length > 0) {
        const selected = event.items[0]
        console.log('[AssignToNewCompanyModal] Selected customer:', selected)
        void handleCustomerSelected(selected.data.id)
      }
    }
  })

  const validateAssignment = async (custId: number): Promise<void> => {
    console.log('[AssignToNewCompanyModal] validateAssignment called with custId:', custId)
    setLoading(true)
    try {
      console.log('[AssignToNewCompanyModal] Calling validateAssignment API...')
      const response = await customerCompanyApi.validateAssignment(custId)
      console.log('[AssignToNewCompanyModal] validateAssignment response:', response)
      if (response.success && response.data) {
        setValidationData(response.data)
        console.log('[AssignToNewCompanyModal] Setting step to form')
        setStep('form')
      } else {
        console.error('[AssignToNewCompanyModal] validateAssignment failed:', response.message)
        message.error(response.message ?? 'Customer cannot be assigned to a company')
        resetState()
      }
    } catch (error) {
      console.error('[AssignToNewCompanyModal] Error in validateAssignment:', error)
      message.error('Failed to validate assignment')
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
        console.error('Failed to check duplicates:', error)
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
        message.success(
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
        message.error(response.message ?? 'Failed to assign customer to company')
      }
    } catch (error) {
      if ((error as any)?.errorFields) {
        return
      }
      message.error('Failed to submit assignment')
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
        </Form>
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
