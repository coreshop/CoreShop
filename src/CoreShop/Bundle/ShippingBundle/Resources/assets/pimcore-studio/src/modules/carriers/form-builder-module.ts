import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createCarrierFormBuilder } from './CarrierFormBuilder'
import { carrierApi } from './api'

export const CarrierFormBuilderModule: AbstractModule = {
  async onInit(): Promise<void> {
    // Load config first, then create builder
    try {
      const config = await carrierApi.getConfig()
      const builder = createCarrierFormBuilder(config)
      container.bind('CoreShop/Shipping/Carrier/FormBuilder').toConstantValue(builder)
    } catch (err) {
      console.error('Failed to load carrier config for FormBuilder:', err)
      // Bind with empty config as fallback
      const builder = createCarrierFormBuilder({ taxCalculationStrategies: [] })
      container.bind('CoreShop/Shipping/Carrier/FormBuilder').toConstantValue(builder)
    }
  }
}
