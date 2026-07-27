/**
 * CoreShop Studio RSBuild Template Configuration
 *
 * Template configuration for individual CoreShop Studio bundles.
 * Configured via environment variables for parallel builds.
 */

import { defineConfig } from '@rsbuild/core'
import { pluginReact } from '@rsbuild/plugin-react'
import { pluginSvgr } from '@rsbuild/plugin-svgr'
import { pluginGenerateEntrypoints } from '@pimcore/studio-ui-bundle/rsbuild/plugins'
import { pluginModuleFederation } from '@module-federation/rsbuild-plugin'
import path from 'path'
import fs from 'fs'
import { v4 } from 'uuid'

// Get bundle configuration from environment variables
const BUNDLE_NAME = process.env.CORESHOP_BUNDLE_NAME
const BUNDLE_DIR = process.env.CORESHOP_BUNDLE_DIR
const BUILD_ID = process.env.CORESHOP_BUILD_ID
const DEV_PORT = process.env.CORESHOP_DEV_PORT

if (!BUNDLE_NAME) {
  throw new Error('CORESHOP_BUNDLE_NAME environment variable is required')
}

// Resolve the actual bundle directory name (preserving original casing)
// CORESHOP_BUNDLE_DIR has the correct casing (e.g. "ProductQuantityPriceRules")
// Fallback: capitalize first letter only (works for single-word names)
const resolvedBundleDir = BUNDLE_DIR ?? (BUNDLE_NAME.charAt(0).toUpperCase() + BUNDLE_NAME.slice(1))

/**
 * Load package.json dependencies for a bundle
 */
function loadBundleDependencies(bundleDir: string): Record<string, any> {
  const packagePath = path.resolve(__dirname, 'src/CoreShop/Bundle', `${bundleDir}Bundle/Resources/assets/pimcore-studio/package.json`)

  try {
    const packageContent = fs.readFileSync(packagePath, 'utf8')
    const pkg = JSON.parse(packageContent)
    return pkg.dependencies || {}
  } catch (error) {
    console.warn(`Warning: Could not load dependencies for ${bundleDir}: ${error}`)
    return {}
  }
}

// Generate configuration for the specified bundle
const bundleDir = path.resolve(__dirname, 'src/CoreShop/Bundle', `${resolvedBundleDir}Bundle/Resources/assets/pimcore-studio`)
const buildId = BUILD_ID || v4()
const buildPath = path.resolve(__dirname, 'src/CoreShop/Bundle', `${resolvedBundleDir}Bundle/Resources/public/studio`, buildId)
const bundlePrefix = `coreshop${BUNDLE_NAME.toLowerCase()}`
const entryFile = './src/main.ts'

// Load bundle-specific dependencies for module federation
const dependencies = loadBundleDependencies(resolvedBundleDir)

// Clean old build directories before creating the new one
const studioPath = path.resolve(__dirname, 'src/CoreShop/Bundle', `${resolvedBundleDir}Bundle/Resources/public/studio`)
if (fs.existsSync(studioPath)) {
  for (const entry of fs.readdirSync(studioPath, { withFileTypes: true })) {
    if (entry.isDirectory()) {
      fs.rmSync(path.resolve(studioPath, entry.name), { recursive: true, force: true })
    }
  }
}

// Ensure build directory exists
if (!fs.existsSync(buildPath)) {
  fs.mkdirSync(buildPath, { recursive: true })
}

let nodeEnv = process.env.NODE_ENV
let env: 'development' | 'production' = 'production'

const isDevServer = nodeEnv === 'dev-server'
if (nodeEnv !== 'production') {
  env = 'development'
}

const devPort = DEV_PORT ? parseInt(DEV_PORT) : (3000 + BUNDLE_NAME.charCodeAt(0) % 100)

// Module Federation options (shared between built-in and plugin)
const moduleFederationOptions: Record<string, any> = {
  name: bundlePrefix,
  dts: false, // Disable DTS generation — the promise-based remote can't be resolved at build time
  filename: 'static/js/remoteEntry.js',
  exposes: {
    '.': entryFile
  },
  remotes: {
    '@pimcore/studio-ui-bundle': `promise new Promise(resolve => {
      const studioUIBundleRemoteUrl = window.StudioUIBundleRemoteUrl
      const script = document.createElement('script')

      let hasScript = false;

      document.querySelectorAll('script').forEach((el) => {
        const elPathname = el.src.replace(/https?:\\/\\/[^/]+/, '')
        const studioUIBundleRemoteUrlPathname = studioUIBundleRemoteUrl.replace(/https?:\\/\\/[^/]+/, '')

        if (elPathname === studioUIBundleRemoteUrlPathname) {
          hasScript = true;
          return;
        }
      })

      if (hasScript) {
        resolve({
          get: (request) => window['pimcore_studio_ui_bundle'].get(request),
          init: (...arg) => {
            try {
              return window['pimcore_studio_ui_bundle'].init(...arg)
            } catch(e) {
              console.log('remote container already initialized')
            }
          }
        })
        return
      }

      script.src = studioUIBundleRemoteUrl
      script.onload = () => {
        const proxy = {
          get: (request) => window['pimcore_studio_ui_bundle'].get(request),
          init: (...arg) => {
            try {
              return window['pimcore_studio_ui_bundle'].init(...arg)
            } catch(e) {
              console.log('remote container already initialized')
            }
          }
        }
        resolve(proxy)
      }
      document.head.appendChild(script);
    })
    `
  },
  shared: {
    ...Object.fromEntries(
      Object.entries(dependencies)
        .filter(
          ([name]) =>
            !name.startsWith('@coreshop/') &&
            name !== '@coreshop/resource-studio-plugin'
        )
        .map(([name, version]) => [
          name,
          {
            singleton: true,
            eager: false,
            requiredVersion: false
          }
        ])
    ),
    // Share CoreShop bundles between all plugins (both internal and external)
    // This ensures registries and other singletons are shared properly
    '@coreshop/resource': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/pimcore': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/currency': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/rule': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/order': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/product': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/shipping': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/payment': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/address': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/taxation': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/store': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/customer': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/core': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@coreshop/studio-form': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    react: {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    'react-dom': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    'react/jsx-runtime': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    'react/jsx-dev-runtime': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    'react-i18next': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    'i18next': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@emotion/react': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    },
    '@emotion/styled': {
      singleton: true,
      eager: false,
      requiredVersion: false,
      strictVersion: false
    }
  }
}

// Main RSBuild configuration for single bundle
export default defineConfig({
  mode: env,
  root: bundleDir,
  performance: {
    buildCache: {
      cacheDirectory: path.resolve(__dirname, 'node_modules/.cache/rsbuild', BUNDLE_NAME),
    }
  },
  server: {
    port: devPort,
    publicDir: {
      copyOnBuild: false
    }
  },
  dev: {
    ...(!isDevServer ? { assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}` } : {}),
    hmr: false,
    client: false,
    liveReload: false,
    lazyCompilation: isDevServer,
    writeToDisk: isDevServer,
  },
  source: {
    entry: {
      main: entryFile
    },
    decorators: {
      version: 'legacy'
    },
    tsconfigPath: path.resolve(__dirname, 'tsconfig.studio.json')
  },
  resolve: {
    alias: {
      [`@CoreShop${resolvedBundleDir}`]: './src',
      [`@CoreShop${resolvedBundleDir}/assets`]: './src/assets',
      // Shared CoreShop bundle aliases for cross-bundle imports
      // Sub-path aliases (must come before main aliases for proper resolution)
      '@coreshop/pimcore/src': path.resolve(__dirname, 'src/CoreShop/Bundle/PimcoreBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/resource/src': path.resolve(__dirname, 'src/CoreShop/Bundle/ResourceBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/currency/src': path.resolve(__dirname, 'src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/rule/src': path.resolve(__dirname, 'src/CoreShop/Bundle/RuleBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/order/src': path.resolve(__dirname, 'src/CoreShop/Bundle/OrderBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/product/src': path.resolve(__dirname, 'src/CoreShop/Bundle/ProductBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/shipping/src': path.resolve(__dirname, 'src/CoreShop/Bundle/ShippingBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/payment/src': path.resolve(__dirname, 'src/CoreShop/Bundle/PaymentBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/address/src': path.resolve(__dirname, 'src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/taxation/src': path.resolve(__dirname, 'src/CoreShop/Bundle/TaxationBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/store/src': path.resolve(__dirname, 'src/CoreShop/Bundle/StoreBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/customer/src': path.resolve(__dirname, 'src/CoreShop/Bundle/CustomerBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/core/src': path.resolve(__dirname, 'src/CoreShop/Bundle/CoreBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/studio-form/src': path.resolve(__dirname, 'src/CoreShop/Bundle/StudioFormBundle/Resources/assets/pimcore-studio/src'),
      // Main entry aliases - use index.ts for library exports (matches package.json "main")
      // main.ts is for Pimcore plugin entry, index.ts is for library exports
      '@coreshop/pimcore': path.resolve(__dirname, 'src/CoreShop/Bundle/PimcoreBundle/Resources/assets/pimcore-studio/src/main.ts'),
      '@coreshop/resource': path.resolve(__dirname, 'src/CoreShop/Bundle/ResourceBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/currency': path.resolve(__dirname, 'src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/rule': path.resolve(__dirname, 'src/CoreShop/Bundle/RuleBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/order': path.resolve(__dirname, 'src/CoreShop/Bundle/OrderBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/product': path.resolve(__dirname, 'src/CoreShop/Bundle/ProductBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/shipping': path.resolve(__dirname, 'src/CoreShop/Bundle/ShippingBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/payment': path.resolve(__dirname, 'src/CoreShop/Bundle/PaymentBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/address': path.resolve(__dirname, 'src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/taxation': path.resolve(__dirname, 'src/CoreShop/Bundle/TaxationBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/store': path.resolve(__dirname, 'src/CoreShop/Bundle/StoreBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/customer': path.resolve(__dirname, 'src/CoreShop/Bundle/CustomerBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/core': path.resolve(__dirname, 'src/CoreShop/Bundle/CoreBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/studio-form': path.resolve(__dirname, 'src/CoreShop/Bundle/StudioFormBundle/Resources/assets/pimcore-studio/src/index.ts')
    }
  },
  output: {
    cleanDistPath: true,
    manifest: true,
    assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}`,
    distPath: {
      root: buildPath
    }
  },
  plugins: [
    pluginModuleFederation(moduleFederationOptions),
    pluginGenerateEntrypoints(),
    pluginReact(),
    pluginSvgr({
      svgrOptions: {
        icon: true,
        typescript: true
      }
    })
  ]
})
