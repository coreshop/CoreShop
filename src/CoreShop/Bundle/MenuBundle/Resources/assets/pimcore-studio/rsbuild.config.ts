import { createCoreShopBundleConfig } from '../../../../ResourceBundle/Resources/assets/pimcore-studio/rsbuild-config-factory'
import packages from './package.json'

export default createCoreShopBundleConfig(
  {
    bundleName: 'menu',
    port: 3034,
    entryFile: './src/index.ts',
    baseDir: __dirname,
  },
  packages
)