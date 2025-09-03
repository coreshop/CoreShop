import { createCoreShopBundleConfig } from './rsbuild-config-factory'
import packages from './package.json'

export default createCoreShopBundleConfig(
  {
    bundleName: 'resource',
    port: 3032,
    entryFile: './src/main.ts',
    baseDir: __dirname
  },
  packages
)