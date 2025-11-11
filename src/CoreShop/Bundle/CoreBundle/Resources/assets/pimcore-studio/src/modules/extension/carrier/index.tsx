/**
 * CoreShop CoreBundle - Carrier Extensions
 *
 * Extends ShippingBundle Carrier with:
 * - stores (Multi-Select)
 * - taxRule (TaxRuleGroup Select)
 */

import React from 'react'
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { Form } from 'antd'
import { entityFormExtensionsServiceId, type EntityFormExtensionRegistry } from '@coreshop/resource/src/entities'
import { StoreSelect } from '@coreshop/store/src/components/StoreSelect'
import { TaxRuleGroupSelect } from '../../../components/TaxRuleGroupSelect'

export const CarrierExtensionModule: AbstractModule = {
  onInit(): void {
    const formRegistry = container.get<EntityFormExtensionRegistry>(entityFormExtensionsServiceId)

    // Add stores multi-select to Carrier form
    formRegistry.add('coreshop.shipping.carrier.form', ({ data, onChange }) => {
      return (
        <>
          <Form.Item
            label="Stores"
            name="stores"
            help="Select stores where this carrier is available"
          >
            <StoreSelect
              mode="multiple"
              value={data?.stores}
              onChange={(value) => onChange({ stores: value as number[] })}
              placeholder="Select stores"
              style={{ width: '100%' }}
            />
          </Form.Item>

          <Form.Item
            label="Tax Rule"
            name="taxRule"
            help="Tax rule for shipping costs"
          >
            <TaxRuleGroupSelect
              value={data?.taxRule}
              onChange={(value) => onChange({ taxRule: value })}
              placeholder="Select tax rule"
              style={{ width: '100%' }}
              allowClear
            />
          </Form.Item>
        </>
      )
    })

  }
}
