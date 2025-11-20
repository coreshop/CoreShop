import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createZoneFormBuilder } from './ZoneFormBuilder'

export const ZoneFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createZoneFormBuilder()
    container.bind('CoreShop/Address/Zone/FormBuilder').toConstantValue(builder)
  }
}
