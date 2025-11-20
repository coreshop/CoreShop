import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createStoreFormBuilder } from './StoreFormBuilder'

export const StoreFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createStoreFormBuilder()
    container.bind('CoreShop/Store/Store/FormBuilder').toConstantValue(builder)
  }
}
