/**
 * CoreShop Studio RSBuild Template Configuration
 * 
 * Template configuration for individual CoreShop Studio bundles.
 * Configured via environment variables for parallel builds.
 */

import { defineConfig } from '@rsbuild/core'
import { pluginReact } from '@rsbuild/plugin-react'
import { pluginSvgr } from '@rsbuild/plugin-svgr'
import { pluginModuleFederation } from '@module-federation/rsbuild-plugin'
import { pluginGenerateEntrypoints } from '@pimcore/studio-ui-bundle/rsbuild/plugins'
import path from 'path'
import fs from 'fs'
import { v4 } from 'uuid'

// Get bundle configuration from environment variables
const BUNDLE_NAME = process.env.CORESHOP_BUNDLE_NAME
const BUILD_ID = process.env.CORESHOP_BUILD_ID
const DEV_PORT = process.env.CORESHOP_DEV_PORT

if (!BUNDLE_NAME) {
  throw new Error('CORESHOP_BUNDLE_NAME environment variable is required')
}

/**
 * Load package.json dependencies for a bundle
 */
function loadBundleDependencies(bundleName: string): Record<string, any> {
  const capitalizedBundle = bundleName.charAt(0).toUpperCase() + bundleName.slice(1)
  const packagePath = path.resolve(__dirname, 'src/CoreShop/Bundle', `${capitalizedBundle}Bundle/Resources/assets/pimcore-studio/package.json`)
  
  try {
    const packageContent = fs.readFileSync(packagePath, 'utf8')
    const pkg = JSON.parse(packageContent)
    return pkg.dependencies || {}
  } catch (error) {
    console.warn(`Warning: Could not load dependencies for ${bundleName}: ${error}`)
    return {}
  }
}

// Generate configuration for the specified bundle
const capitalizedBundle = BUNDLE_NAME.charAt(0).toUpperCase() + BUNDLE_NAME.slice(1)
const bundleDir = path.resolve(__dirname, 'src/CoreShop/Bundle', `${capitalizedBundle}Bundle/Resources/assets/pimcore-studio`)
const buildId = BUILD_ID || v4()
const buildPath = path.resolve(__dirname, 'src/CoreShop/Bundle', `${capitalizedBundle}Bundle/Resources/public/studio`, buildId)
const bundlePrefix = `coreshop${BUNDLE_NAME.toLowerCase()}`
const entryFile = './src/main.ts'

// Load bundle-specific dependencies for module federation
const dependencies = loadBundleDependencies(BUNDLE_NAME)

// Clean old build directories (following rsbuild-config-factory.ts.bak pattern)
const studioPath = path.resolve(__dirname, 'src/CoreShop/Bundle', `${capitalizedBundle}Bundle/Resources/public/studio`)
if (fs.existsSync(studioPath)) {
  fs.readdirSync(studioPath).forEach((file) => {
    const filePath = path.resolve(studioPath, file)
    if (fs.statSync(filePath).isDirectory()) {
      fs.rmSync(filePath, { recursive: true, force: true })
    }
  })
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

// Main RSBuild configuration for single bundle (following rsbuild-config-factory.ts.bak pattern)
export default defineConfig({
  mode: env,
  root: bundleDir,
  server: {
    port: devPort,
    publicDir: {
      copyOnBuild: false
    }
  },
  dev: {
    ...(!isDevServer ? { assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}` } : {}),
    client: {
      host: 'localhost',
      port: devPort,
      protocol: 'ws'
    },
    hmr: true,
  },
  source: {
    entry: {
      main: entryFile
    },
    decorators: {
      version: 'legacy'
    }
  },
  resolve: {
    alias: {
      [`@CoreShop${capitalizedBundle}`]: './src',
      [`@CoreShop${capitalizedBundle}/assets`]: './src/assets',
      // Shared CoreShop bundle aliases for cross-bundle imports
      // Sub-path aliases (must come before main aliases for proper resolution)
      '@coreshop/pimcore/src': path.resolve(__dirname, 'src/CoreShop/Bundle/PimcoreBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/resource/src': path.resolve(__dirname, 'src/CoreShop/Bundle/ResourceBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/currency/src': path.resolve(__dirname, 'src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src'),
      '@coreshop/rule/src': path.resolve(__dirname, 'src/CoreShop/Bundle/RuleBundle/Resources/assets/pimcore-studio/src'),
      // Main entry aliases - use index.ts for library exports (matches package.json "main")
      // main.ts is for Pimcore plugin entry, index.ts is for library exports
      '@coreshop/pimcore': path.resolve(__dirname, 'src/CoreShop/Bundle/PimcoreBundle/Resources/assets/pimcore-studio/src/main.ts'),
      '@coreshop/resource': path.resolve(__dirname, 'src/CoreShop/Bundle/ResourceBundle/Resources/assets/pimcore-studio/src/index.ts'),
      '@coreshop/currency': path.resolve(__dirname, 'src/CoreShop/Bundle/CurrencyBundle/Resources/assets/pimcore-studio/src/main.ts'),
      '@coreshop/rule': path.resolve(__dirname, 'src/CoreShop/Bundle/RuleBundle/Resources/assets/pimcore-studio/src/main.ts')
    }
  },
  output: {
    manifest: true,
    assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}`,
    distPath: {
      root: buildPath
    }
  },
  tools: {
    bundlerChain: (chain, { env }) => {
      chain.output.uniqueName(bundlePrefix)
    }
  },
  plugins: [
    pluginGenerateEntrypoints(),
    pluginReact(),
    pluginSvgr({
      svgrOptions: {
        icon: true,
        typescript: true
      }
    }),
    pluginModuleFederation({
      name: bundlePrefix,
      filename: 'static/js/remoteEntry.js',
      exposes: {
        '.': entryFile
      },
      dts: false,
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
        // Share CoreShop ResourceBundle between all bundles (for DynamicForm, etc.)
        '@coreshop/resource': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        // Share CoreShop PimcoreBundle between all bundles (for broker event communication)
        '@coreshop/pimcore': {
          singleton: true,
          eager: false,
          requiredVersion: false,
          strictVersion: false
        },
        // Share CoreShop CurrencyBundle between all bundles (for price formatting config)
        '@coreshop/currency': {
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
    })
  ]
})
