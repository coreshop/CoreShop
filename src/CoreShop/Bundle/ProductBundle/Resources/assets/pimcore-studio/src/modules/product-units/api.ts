import { EntityApi } from '@coreshop/resource/src/entities'

export interface ProductUnitTranslation {
  locale: string
  fullLabel?: string
  fullPluralLabel?: string
  shortLabel?: string
  shortPluralLabel?: string
}

export interface ProductUnitDetail extends Record<string, any> {
  id: number
  name: string
  translations?: ProductUnitTranslation[]
  fullLabel?: string
  fullPluralLabel?: string
  shortLabel?: string
  shortPluralLabel?: string
}

export const productUnitApi = new EntityApi<ProductUnitDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/product_units'
})
