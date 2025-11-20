import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createTaxRateFormBuilder } from './TaxRateFormBuilder'

export const TaxRateFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createTaxRateFormBuilder()
    container.bind('CoreShop/Taxation/TaxRate/FormBuilder').toConstantValue(builder)
  }
}
