/**
 * CoreShop Customer to Existing Company Assignment Panel
 *
 * Detail widget that receives customerId + companyId from widget config.
 * Loads details and validation on mount, then shows the form.
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
  `
}))

type Step = 'loading' | 'form' | 'success' | 'error'

const defaultFormData: AssignmentFormValues = {
  addressAssignmentType: '',
  addressAccessType: 'own_only',
}

interface AssignToExistingCompanyPanelProps {
  customerId?: number
  companyId?: number
}

export const AssignToExistingCompanyPanel: React.FC<AssignToExistingCompanyPanelProps> = ({ customerId, companyId }) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()

  const [step, setStep] = React.useState<Step>('loading')
  const [submitting, setSubmitting] = React.useState(false)

  const [customerData, setCustomerData] = React.useState<EntityDetails | null>(null)
  const [companyData, setCompanyData] = React.useState<EntityDetails | null>(null)
  const [validationData, setValidationData] = React.useState<ValidationData | null>(null)

  const [formData, setFormData] = React.useState<AssignmentFormValues>({ ...defaultFormData })

  React.useEffect(() => {
    if (!customerId || !companyId) {
      setStep('error')
      return
    }

    const loadData = async () => {
      try {
        const [customerResponse, companyResponse, validationResponse] = await Promise.all([
          customerCompanyApi.getEntityDetails('customer', customerId),
          customerCompanyApi.getEntityDetails('company', companyId),
          customerCompanyApi.validateAssignment(customerId, companyId),
        ])

        if (customerResponse.success && customerResponse.data) {
          setCustomerData(customerResponse.data)
        }

        if (companyResponse.success && companyResponse.data) {
          setCompanyData(companyResponse.data)
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
          messageApi.error(renderApiError(validationResponse.message ?? 'Customer cannot be assigned to this company'))
          setStep('error')
        }
      } catch (error) {
        messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load details')))
        setStep('error')
      }
    }

    loadData()
  }, [customerId, companyId])

  const handleSubmit = async (): Promise<void> => {
    if (!customerId || !companyId) return
    if (!formData.addressAssignmentType) {
      messageApi.error(renderApiError(t('field_required', { defaultValue: 'This field is required' })))
      return
    }

    setSubmitting(true)
    try {
      const response = await customerCompanyApi.dispatchExistingAssignment(customerId, companyId, {
        addressAssignmentType: formData.addressAssignmentType,
        addressAccessType: formData.addressAccessType,
      })

      if (response.success) {
        messageApi.success(
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
        messageApi.error(renderApiError(response.message ?? 'Failed to assign customer to company'))
      }
    } catch (error) {
      messageApi.error(renderApiError(getErrorMessage(error, 'Failed to submit assignment')))
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
            defaultValue: 'Could not load data. Please close this tab and try again.',
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
