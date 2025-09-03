import { createCoreShopBundleConfig } from '../../../../ResourceBundle/Resources/assets/pimcore-studio/rsbuild-config-factory'
import packages from './package.json'

export default createCoreShopBundleConfig(
  {
    bundleName: 'address',
    port: 3033,
    entryFile: './src/main.ts',
    baseDir: __dirname
  },
  packages
)