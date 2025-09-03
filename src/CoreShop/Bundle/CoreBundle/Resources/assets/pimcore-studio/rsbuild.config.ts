import { createCoreShopBundleConfig } from '../../../../ResourceBundle/Resources/assets/pimcore-studio/rsbuild-config-factory'
import packages from './package.json'

export default createCoreShopBundleConfig(
  {
    bundleName: 'core',
    port: 3039,
    entryFile: './src/main.ts',
    baseDir: __dirname
  },
  packages
)