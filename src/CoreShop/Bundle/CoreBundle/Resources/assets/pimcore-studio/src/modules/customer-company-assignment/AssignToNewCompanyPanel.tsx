/**
 * CoreShop Customer to New Company Assignment Panel
 *
 * Detail widget that receives customerId from widget config.
 * Loads customer details and validation on mount, then shows the form.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Button, Spin, Result } from 'antd'
import { CheckOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { AssignmentForm, type AssignmentFormValues } from './AssignmentForm'
import { customerCompanyApi, type EntityDetails, type ValidationData, type DuplicateCompany } from './api'
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
  `
}))

type Step = 'loading' | 'form' | 'success' | 'error'

const defaultFormData: AssignmentFormValues = {
  addressAssignmentType: '',
  addressAccessType: 'own_only',
  newCompanyName: '',
}

interface AssignToNewCompanyPanelProps {
  customerId?: number
}

export const AssignToNewCompanyPanel: React.FC<AssignToNewCompanyPanelProps> = ({ customerId }) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()

  const [step, setStep] = React.useState<Step>('loading')
  const [submitting, setSubmitting] = React.useState(false)

  const [customerData, setCustomerData] = React.useState<EntityDetails | null>(null)
  const [validationData, setValidationData] = React.useState<ValidationData | null>(null)

  const [formData, setFormData] = React.useState<AssignmentFormValues>({ ...defaultFormData })
  const [duplicates, setDuplicates] = React.useState<DuplicateCompany[]>([])
  const [showDuplicates, setShowDuplicates] = React.useState(false)
  const [checkingDuplicates, setCheckingDuplicates] = React.useState(false)

  const debounceRef = React.useRef<ReturnType<typeof setTimeout> | null>(null)

  React.useEffect(() => {
    if (!customerId) {
      setStep('error')
      return
    }

    const loadData = async () => {
      try {
        const [customerResponse, validationResponse] = await Promise.all([
          customerCompanyApi.getEntityDetails('customer', customerId),
          customerCompanyApi.validateAssignment(customerId),
        ])

        if (customerResponse.success && customerResponse.data) {
          setCustomerData(customerResponse.data)
        }

        if (validationResponse.success && validationResponse.data) {
          setValidationData(validationResponse.data)
          const hasAddresses = validationResponse.data.addresses.length > 0
          setFormData({
            ...defaultFormData,
            addressAssignmentType: hasAddresses ? '' : 'keep',
          })
          setStep('form')
        } else {
          void messageApi.error(renderApiError(validationResponse.message ?? 'Customer cannot be assigned to a company'))
          setStep('error')
        }
      } catch (error) {
        void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load customer details')))
        setStep('error')
      }
    }

    void loadData()
  }, [customerId])

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

  if (step === 'loading') {
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
        />
      </div>
    )
  }

  if (step === 'error') {
    return (
      <div className={styles.container}>
        <Result
          status="error"
          title={t('coreshop_customer_transformer_error', {
            defaultValue: 'Error',
          })}
          subTitle={t('coreshop_customer_transformer_error_description', {
            defaultValue: 'Could not load customer data. Please close this tab and try again.',
          })}
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
          addresses={validationData?.addresses ?? []}
          isNewCompany={true}
          formData={formData}
          onFormChange={setFormData}
          onCompanyNameChange={handleCompanyNameChange}
          duplicates={duplicates}
          showDuplicates={showDuplicates}
        />

        <Button
          type="primary"
          icon={<CheckOutlined />}
          onClick={handleSubmit}
          loading={submitting || checkingDuplicates}
          style={{ marginTop: 16 }}
        >
          {t('coreshop_customer_transformer_assignment_new_form_button', {
            defaultValue: 'Create Company and assign Customer',
          })}
        </Button>
      </div>
    </div>
  )
}