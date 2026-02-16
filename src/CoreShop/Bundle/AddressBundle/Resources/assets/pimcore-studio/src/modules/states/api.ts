import { EntityApi } from '@coreshop/resource/src/entities'

export interface StateDetail extends Record<string, any> {
  id: number
  name: string
  active: boolean
  isoCode?: string
  country?: number
  countryName?: string
  translations?: Record<string, { locale: string, name: string }>
}

export const stateApi = new EntityApi<StateDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/states'
})

