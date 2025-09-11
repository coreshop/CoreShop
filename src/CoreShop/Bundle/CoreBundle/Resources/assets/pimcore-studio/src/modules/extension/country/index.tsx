/**
 * Registers the Currency Select field into AddressBundle Country form via extension registry
 */

import React from 'react'
import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { CurrencySelect } from '@coreshop/currency/src/components/CurrencySelect'
const entityFormExtensionsServiceId = 'CoreShop/Studio/EntityFormExtensions'

export const CountryExtensionModule: AbstractModule = {
    onInit(): void {
        const registryForm = container.get<any>(entityFormExtensionsServiceId)
        registryForm?.add?.('coreshop.address.country.form', () => <CurrencySelect />)

    }
}
