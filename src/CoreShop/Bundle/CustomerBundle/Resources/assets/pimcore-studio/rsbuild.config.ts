import { createCoreShopBundleConfig } from '../../../../ResourceBundle/Resources/assets/pimcore-studio/rsbuild-config-factory'
import packages from './package.json'

export default createCoreShopBundleConfig(
  {
    bundleName: 'customer',
    port: 3037,
    entryFile: './src/main.ts',
    baseDir: __dirname
  },
  packages
)