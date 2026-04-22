/**
 * CoreShop RSBuild Config Factory
 *
 * Shared utility to generate RSBuild configurations for CoreShop bundles
 */

import {defineConfig, RsbuildConfig} from '@rsbuild/core'
import {pluginReact} from '@rsbuild/plugin-react'
import {pluginModuleFederation} from '@module-federation/rsbuild-plugin'
import {pluginGenerateEntrypoints} from '@pimcore/studio-ui-bundle/rsbuild/plugins'
import { pluginSvgr } from '@rsbuild/plugin-svgr';
import path from 'path'
import fs from 'fs'
import {v4} from 'uuid'

export interface CoreShopBundleConfig {
    /**
     * Bundle name (e.g., 'menu', 'resource', 'address')
     */
    bundleName: string

    /**
     * Development server port
     */
    port: number

    /**
     * Entry file path relative to src directory
     * @default './src/index.ts'
     */
    entryFile?: string

    /**
     * Base directory where the rsbuild config is located
     * Use __dirname from the calling config file
     */
    baseDir: string,
}

/**
 * Creates a standardized RSBuild configuration for CoreShop bundles
 */
export function createCoreShopBundleConfig(config: CoreShopBundleConfig, packages: any): RsbuildConfig {
    const {bundleName, port, entryFile = './src/index.ts', baseDir} = config

    const buildId = v4()
    const buildPath = path.resolve(baseDir, '..', '..', 'public', 'studio', buildId)
    const bundlePrefix = `coreshop${bundleName.toLowerCase()}`

    // Clean old build directories
    const studioPath = path.resolve(baseDir, '..', '..', 'public', 'studio')
    if (fs.existsSync(studioPath)) {
        fs.readdirSync(studioPath).forEach((file) => {
            const filePath = path.resolve(studioPath, file)
            if (fs.statSync(filePath).isDirectory()) {
                fs.rmSync(filePath, {recursive: true, force: true})
            }
        })
    }

    // Ensure build directory exists
    if (!fs.existsSync(buildPath)) {
        fs.mkdirSync(buildPath, {recursive: true})
    }

    let nodeEnv = process.env.NODE_ENV;
    let env: 'development' | 'production' = 'production';

    const isDevServer = nodeEnv === 'dev-server';
    if (nodeEnv !== env) {
        env = 'development';
    }

    return defineConfig({
        mode: env,
        server: {
            port,
        },
        dev: {
            ...(!isDevServer ? {assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}`} : {}),
            client: {
                host: 'localhost',
                port,
                protocol: 'ws'
            }
        },
        source: {
            entry: {
                main: entryFile
            },
            decorators: {
                version: 'legacy'
            },
            alias: {
                [`@CoreShop${bundleName.charAt(0).toUpperCase() + bundleName.slice(1)}`]: path.resolve(baseDir, 'src'),
                [`@CoreShop${bundleName.charAt(0).toUpperCase() + bundleName.slice(1)}/assets`]: path.resolve(baseDir, 'src/assets')
            }
        },
        output: {
            manifest: true,
            assetPrefix: `/bundles/${bundlePrefix}/studio/${buildId}`,
            distPath: {
                root: buildPath
            },
        },
        tools: {
            bundlerChain: (chain, {env}) => {
                chain.output.uniqueName(bundlePrefix)
            },
        },
        plugins: [
            pluginGenerateEntrypoints(),
            pluginReact(),
            pluginSvgr({
                svgrOptions: {
                    icon: true,
                    typescript: true,
                }
            }),
            pluginModuleFederation({
                name: bundlePrefix,
                filename: 'static/js/remoteEntry.js',
                exposes: {
                    '.': entryFile,
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
        `,
                },
                shared: {
                    ...packages.dependencies,
                    react: {
                        singleton: true,
                        eager: true,
                        requiredVersion: false,
                    },
                    'react-dom': {
                        singleton: true,
                        eager: true,
                        requiredVersion: false,
                    }
                },
            })
        ]
    })
}
