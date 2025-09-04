/**
 * CoreShop Menu Bundle - Pimcore Studio Plugin
 * 
 * Main entry point that registers the CoreShop Menu Extension module
 * following the Pimcore Studio plugin pattern
 */

import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle'
import { CoreShopMenuExtension } from './modules/menu-extension'

export { MenuButtonRegistry } from './services/button-registry';

const CoreShopMenuPlugin: IAbstractPlugin = {
  name: 'CoreShopMenuPlugin',
  
  onStartup({moduleSystem}): void {
    moduleSystem.registerModule(CoreShopMenuExtension)
  }
}

export default CoreShopMenuPlugin