import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createShippingRuleFormBuilder } from './ShippingRuleFormBuilder'

export const ShippingRuleFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createShippingRuleFormBuilder()
    container.bind('CoreShop/Shipping/ShippingRule/FormBuilder').toConstantValue(builder)
  }
}
