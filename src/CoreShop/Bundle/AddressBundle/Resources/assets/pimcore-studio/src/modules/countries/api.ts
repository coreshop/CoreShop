import { EntityApi } from '@coreshop/resource/src/entities'

export interface CountryDetail extends Record<string, any> {
  id: number
  name: string
  active: boolean
  zone?: number
  isoCode?: string
  zoneName?: string
  addressFormat?: string
  salutations?: string[]
  currency?: number
  translations?: Record<string, { locale: string, name: string }>
}

export const countryApi = new EntityApi<CountryDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/countries'
})
