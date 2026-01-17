import { EntityApi } from '@coreshop/resource/src/entities/api'

export interface AddressIdentifierDetail {
  id: number
  name: string
  active: boolean
}

export const addressIdentifierApi = new EntityApi<AddressIdentifierDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/address_identifiers'
})
