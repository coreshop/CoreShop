import { createCoreShopBundleConfig } from '../../../../ResourceBundle/Resources/assets/pimcore-studio/rsbuild-config-factory'
import packages from './package.json'

export default createCoreShopBundleConfig(
  {
    bundleName: 'pimcore',
    port: 3049,
    entryFile: './src/main.ts',
    baseDir: __dirname
  },
  packages
)