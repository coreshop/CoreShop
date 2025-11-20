import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createCartPriceRuleFormBuilder } from './CartPriceRuleFormBuilder'

export const CartPriceRuleFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createCartPriceRuleFormBuilder()
    container.bind('CoreShop/Order/CartPriceRule/FormBuilder').toConstantValue(builder)
  }
}
