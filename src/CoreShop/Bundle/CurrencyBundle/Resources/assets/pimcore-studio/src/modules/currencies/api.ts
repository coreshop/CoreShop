import { EntityApi } from '@coreshop/resource/src/entities'

export interface CurrencyDetail extends Record<string, any> {
  id: number
  name: string
  isoCode?: string
  numericIsoCode?: number
  symbol?: string
}

export const currencyApi = new EntityApi<CurrencyDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/currencies'
})
