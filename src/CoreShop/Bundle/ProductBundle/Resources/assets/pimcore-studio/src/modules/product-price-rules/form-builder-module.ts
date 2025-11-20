import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createProductPriceRuleFormBuilder } from './ProductPriceRuleFormBuilder'

export const ProductPriceRuleFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createProductPriceRuleFormBuilder()
    container.bind('CoreShop/Product/ProductPriceRule/FormBuilder').toConstantValue(builder)
  }
}
