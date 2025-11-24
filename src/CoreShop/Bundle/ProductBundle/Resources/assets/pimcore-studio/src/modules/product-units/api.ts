import { EntityApi } from '@coreshop/resource/src/entities'

export interface ProductUnitTranslation {
  locale: string
  fullLabel?: string
  fullPluralLabel?: string
  shortLabel?: string
  shortPluralLabel?: string
}

export interface ProductUnitDetail {
  id: number
  name: string
  translations?: ProductUnitTranslation[]
}

export const productUnitApi = new EntityApi<ProductUnitDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/product_units'
})
