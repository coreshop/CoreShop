import { EntityApi } from '@coreshop/resource/src/entities/api'

export interface AddressIdentifierDetail extends Record<string, any> {
  id: number
  name: string
  active: boolean
}

export const addressIdentifierApi = new EntityApi<AddressIdentifierDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/address_identifiers'
})
