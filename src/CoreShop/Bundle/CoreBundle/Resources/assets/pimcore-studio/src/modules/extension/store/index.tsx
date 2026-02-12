/**
 * Registers the Store extensions (Base Country, Gross Price, Allowed Countries)
 *
 * Extension fields are automatically included in save payload via onChange/setData
 * No separate save decorator needed - data flows through DynamicForm -> onChange -> data -> buildSavePayload
 */

import { Checkbox } from 'antd'
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import type { FormBuilder } from '@coreshop/studio-form/src/form-builder'
import { addFieldDecorator } from '@coreshop/studio-form/src/form-builder'
import { CountrySelectField, CountryMultiSelectField } from '@coreshop/address/src'
import type { ExtendedStoreDetail } from './types'

export const StoreExtensionModule: AbstractModule = {
    onInit(): void {
        try {
            const builder = container.get<FormBuilder<ExtendedStoreDetail>>('CoreShop/Store/Store/FormBuilder')

            // Add Base Country field
            builder.addDecorator('baseCountry-field', addFieldDecorator({
                name: 'baseCountry',
                label: 'coreshop_base_country',
                component: CountrySelectField,
                componentProps: {
                    allowClear: true
                }
            }))

            // Add Use Gross Price checkbox
            builder.addDecorator('useGrossPrice-field', addFieldDecorator({
                name: 'useGrossPrice',
                label: 'coreshop_use_gross_prices',
                component: Checkbox,
                componentProps: {
                    children: 'Use Gross Prices'
                }
            }))

            // Add Allowed Countries field
            builder.addDecorator('countries-field', addFieldDecorator({
                name: 'countries',
                label: 'coreshop_allowed_countries',
                component: CountryMultiSelectField
            }))
        } catch (err) {
            console.error('[CoreBundle] Failed to extend Store FormBuilder:', err)
        }
    }
}
