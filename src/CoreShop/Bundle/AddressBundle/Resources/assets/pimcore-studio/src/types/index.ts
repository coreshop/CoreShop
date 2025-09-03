/**
 * CoreShop Address Bundle Types
 * 
 * TypeScript type definitions for address-related functionality
 */

import { CoreShopResource } from '@coreshop/resource-studio-plugin'

export interface Country extends CoreShopResource {
  name: string
  isoCode: string
  active: boolean
  zone?: Zone
  zoneName?: string
  states?: State[]
}

export interface State extends CoreShopResource {
  name: string
  isoCode: string
  active: boolean
  country?: Country
  countryId?: number
}

export interface Zone extends CoreShopResource {
  name: string
  active: boolean
  countries?: Country[]
}

export interface CountrySalutation extends CoreShopResource {
  name: string
  country?: Country
  countryId?: number
}

export interface AddressIdentifier extends CoreShopResource {
  name: string
  pattern: string
}

// Form field types for Pimcore data objects
export interface CoreShopAddressIdentifierField {
  type: 'coreShopAddressIdentifier'
  value: AddressIdentifier | null
}

export interface CoreShopCountryField {
  type: 'coreShopCountry' 
  value: Country | null
}

export interface CoreShopCountryMultiselectField {
  type: 'coreShopCountryMultiselect'
  value: Country[]
}

export interface CoreShopStateField {
  type: 'coreShopState'
  value: State | null
}

// API response types
export interface CountryListResponse {
  success: boolean
  data: Country[]
}

export interface StateListResponse {
  success: boolean
  data: State[]
}

export interface ZoneListResponse {
  success: boolean
  data: Zone[]
}