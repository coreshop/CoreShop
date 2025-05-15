import { Pimcore } from 'pimcore-studio-ui';
import { CoreShopPlugin} from "./CoreShop";

if (module.hot !== undefined) {
    module.hot.accept()
}


const pluginSystem = Pimcore.pluginSystem

pluginSystem.registerPlugin(CoreShopPlugin)
