import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createPaymentProviderRuleFormBuilder } from './PaymentProviderRuleFormBuilder'

export const PaymentProviderRuleFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createPaymentProviderRuleFormBuilder()
    container.bind('CoreShop/Payment/PaymentProviderRule/FormBuilder').toConstantValue(builder)
  }
}
