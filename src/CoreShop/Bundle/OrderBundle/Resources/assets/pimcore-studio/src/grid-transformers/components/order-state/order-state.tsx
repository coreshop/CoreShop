/**
 * CoreShop OrderBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { useTranslation } from 'react-i18next'
import { Form, Select, Switch } from '@pimcore/studio-ui-bundle/components'

export const OrderStateTransformerComponent = (): React.JSX.Element => {
  const { t } = useTranslation()

  const workflowOptions = [
    { value: 'coreshop_order', label: t('coreshop_grid_transformer_order_state_workflow_order') },
    { value: 'coreshop_order_payment', label: t('coreshop_grid_transformer_order_state_workflow_payment') },
    { value: 'coreshop_order_shipment', label: t('coreshop_grid_transformer_order_state_workflow_shipment') },
    { value: 'coreshop_order_invoice', label: t('coreshop_grid_transformer_order_state_workflow_invoice') }
  ]

  return (
    <>
      <Form.Item
        label={t('coreshop_grid_transformer_order_state_workflow')}
        name='workflow'
        rules={[{ required: true, message: t('coreshop_grid_transformer_order_state_workflow_required') }]}
      >
        <Select options={workflowOptions} />
      </Form.Item>
      <Form.Item
        initialValue={false}
        label={t('coreshop_grid_transformer_order_state_highlight_label')}
        name='highlightLabel'
        valuePropName='checked'
      >
        <Switch />
      </Form.Item>
    </>
  )
}
