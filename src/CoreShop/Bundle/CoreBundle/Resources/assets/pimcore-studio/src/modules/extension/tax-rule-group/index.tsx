/**
 * CoreShop CoreBundle Studio Plugin - TaxRuleGroup Extensions
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { CountrySelectInput } from '../../../components/CountrySelectInput'
import { StateSelectInput } from '../../../components/StateSelectInput'
import { entityTableColumnExtensionsServiceId } from '@coreshop/resource/src/entities'

export const TaxRuleGroupExtensionModule: AbstractModule = {
  onInit(): void {
    const tableColumnRegistry = container.get<any>(entityTableColumnExtensionsServiceId)
    
    // Register table column extensions for TaxRuleGroup tax rules
    tableColumnRegistry?.add?.('coreshop.taxation.tax_rule_group.tax_rules', ({ updateRecord }: any) => [
      {
        title: 'Country',
        dataIndex: 'country',
        width: 150,
        render: (value: number | undefined, record: any, index: number) => (
          <CountrySelectInput
            value={value}
            onChange={(newValue) => updateRecord(index, 'country', newValue)}
            style={{ width: '100%' }}
            allowClear
          />
        )
      },
      {
        title: 'State',
        dataIndex: 'state',
        width: 150,
        render: (value: number | undefined, record: any, index: number) => (
          <StateSelectInput
            value={value}
            countryId={record.country}
            onChange={(newValue) => updateRecord(index, 'state', newValue)}
            style={{ width: '100%' }}
            allowClear
          />
        )
      }
    ])
  }
}