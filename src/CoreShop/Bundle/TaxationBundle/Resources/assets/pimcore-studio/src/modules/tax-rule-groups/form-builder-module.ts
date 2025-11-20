import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createTaxRuleGroupFormBuilder } from './TaxRuleGroupFormBuilder'

export const TaxRuleGroupFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createTaxRuleGroupFormBuilder()
    container.bind('CoreShop/Taxation/TaxRuleGroup/FormBuilder').toConstantValue(builder)
  }
}
