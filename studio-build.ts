#!/usr/bin/env node

/**
 * CoreShop Studio Build Script
 * 
 * Builds all CoreShop Studio plugins in parallel
 */

import { execSync } from 'child_process'
import crypto from 'crypto'
import path from 'path'
import fs from 'fs'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

interface Plugin {
  name: string
  path: string
  color: string
}

/**
 * Root files that influence the output of every bundle build.
 */
const rootConfigFiles = [
  'rsbuild.template.config.ts',
  'package.json',
  'package-lock.json',
  'tsconfig.json',
  'tsconfig.studio.json',
  'studio-build.ts'
]

/**
 * Build a map of bundle -> the bundles it depends on, resolved from the
 * `file:` entries in its package.json.
 */
function buildBundleDepsMap(plugins: Plugin[]): Map<string, string[]> {
  const bundleDeps = new Map<string, string[]>()

  for (const plugin of plugins) {
    const pkgPath = path.resolve(__dirname, plugin.path, 'package.json')
    try {
      const pkg = JSON.parse(fs.readFileSync(pkgPath, 'utf8'))
      const deps = Object.values({ ...pkg.dependencies, ...pkg.devDependencies }) as string[]
      const fileDeps: string[] = []
      for (const dep of deps) {
        if (dep.startsWith('file:')) {
          // Extract bundle name from file: path like "file:../../ResourceBundle/Resources/assets/pimcore-studio"
          const depMatch = dep.match(/(\w+Bundle)\/Resources\/assets\/pimcore-studio/)
          if (depMatch) {
            fileDeps.push(depMatch[1])
          }
        }
      }
      bundleDeps.set(plugin.name, fileDeps)
    } catch {
      // If package.json can't be read, no deps
    }
  }

  return bundleDeps
}

/**
 * Get the list of bundles changed since the base branch.
 * Returns null if all bundles should be built (e.g. root config changed).
 */
function getChangedBundles(plugins: Plugin[]): Plugin[] | null {
  // Detect base branch from environment or default
  const baseBranch = process.env.GITHUB_BASE_REF || process.env.BASE_BRANCH || 'master'

  let changedFiles: string[]
  try {
    const result = execSync(`git diff --name-only origin/${baseBranch}...HEAD`, {
      encoding: 'utf8',
      stdio: ['pipe', 'pipe', 'pipe']
    })
    changedFiles = result.trim().split('\n').filter(Boolean)
  } catch {
    // If git diff fails (e.g. shallow clone, no remote), build everything
    console.log('Could not determine changed files, building all bundles')
    return null
  }

  if (changedFiles.length === 0) {
    return []
  }

  // If root build config changed, rebuild everything
  if (changedFiles.some(f => rootConfigFiles.includes(f))) {
    console.log('Root config changed, building all bundles')
    return null
  }

  // Map changed files to affected bundles
  const changedBundleNames = new Set<string>()
  for (const file of changedFiles) {
    const match = file.match(/^src\/CoreShop\/Bundle\/(\w+Bundle)\//)
    if (match) {
      changedBundleNames.add(match[1])
    }
  }

  if (changedBundleNames.size === 0) {
    return []
  }

  // Expand dependency graph: if a dependency changed, dependents must rebuild too
  const bundleDeps = buildBundleDepsMap(plugins)

  // Iteratively expand: if any dependency is in changedBundleNames, add the dependent
  let expanded = true
  while (expanded) {
    expanded = false
    for (const [bundle, deps] of bundleDeps) {
      if (!changedBundleNames.has(bundle)) {
        for (const dep of deps) {
          if (changedBundleNames.has(dep)) {
            changedBundleNames.add(bundle)
            expanded = true
            break
          }
        }
      }
    }
  }

  return plugins.filter(p => changedBundleNames.has(p.name))
}

/**
 * Collect the transitive `file:` dependency closure of a bundle, including the bundle itself.
 */
function collectDependencyClosure(bundleName: string, bundleDeps: Map<string, string[]>): string[] {
  const closure = new Set<string>()
  const queue = [bundleName]

  while (queue.length > 0) {
    const current = queue.shift()!
    if (closure.has(current)) {
      continue
    }
    closure.add(current)
    queue.push(...(bundleDeps.get(current) ?? []))
  }

  return [...closure].sort()
}

/**
 * Recursively collect the source files of a bundle's studio assets, relative to the repo root.
 */
function collectSourceFiles(dir: string, files: string[] = []): string[] {
  const ignored = new Set(['node_modules', 'dist', '.rsbuild', '.turbo'])

  let entries: fs.Dirent[]
  try {
    entries = fs.readdirSync(dir, { withFileTypes: true })
  } catch {
    return files
  }

  for (const entry of entries) {
    if (ignored.has(entry.name)) {
      continue
    }

    const full = path.resolve(dir, entry.name)
    if (entry.isDirectory()) {
      collectSourceFiles(full, files)
    } else if (entry.isFile()) {
      files.push(full)
    }
  }

  return files
}

/**
 * Derive a deterministic build id for a bundle from its sources.
 *
 * The build id doubles as the output directory name and as the asset prefix, so it has to
 * change whenever the emitted assets change (cache busting) — but *only* then. Using a random
 * id per build made every build a full delete/add of all assets in git, which drowned out the
 * actual changes in pull requests.
 *
 * Inputs are the bundle's own sources, the sources of every bundle it depends on (they get
 * inlined via the aliases in rsbuild.template.config.ts) and the root build configuration.
 */
function computeBuildId(plugin: Plugin, plugins: Plugin[], bundleDeps: Map<string, string[]>): string {
  const hash = crypto.createHash('sha256')
  const pluginsByName = new Map(plugins.map(p => [p.name, p]))

  for (const file of rootConfigFiles) {
    const full = path.resolve(__dirname, file)
    if (fs.existsSync(full)) {
      hash.update(`${file}\0`)
      hash.update(fs.readFileSync(full))
    }
  }

  for (const bundleName of collectDependencyClosure(plugin.name, bundleDeps)) {
    const dependency = pluginsByName.get(bundleName)
    if (!dependency) {
      continue
    }

    const root = path.resolve(__dirname, dependency.path)
    const files = collectSourceFiles(root).sort()

    for (const file of files) {
      // Hash the path relative to the repo root so the id is independent of the checkout location
      hash.update(`${path.relative(__dirname, file).split(path.sep).join('/')}\0`)
      hash.update(fs.readFileSync(file))
    }
  }

  return hash.digest('hex').slice(0, 32)
}

/**
 * Package each freshly built bundle into Resources/build-dist/build-<id>.zip.
 *
 * Only the archive is committed; the expanded build in Resources/public/studio is gitignored
 * and reconstructed from the archive at cache warmup by Pimcore's BuildArchiveExtractor (see
 * the bundles' Studio/WebpackEntryPointProvider). The packaging itself is Pimcore's
 * `studio-package-build`, which expects a `.build-id` file in the output directory: it keeps
 * an existing archive for that id untouched — the compiled output is not byte-reproducible,
 * but the id is source-derived, so an unchanged id means unchanged sources — and replaces
 * the previous archive when the id is new. One archive per bundle is ever tracked.
 */
function packageBuilds(buildPlugins: Plugin[], plugins: Plugin[], bundleDeps: Map<string, string[]>): void {
  const packageBin = path.resolve(__dirname, 'node_modules/.bin/studio-package-build')

  for (const plugin of buildPlugins) {
    const buildId = computeBuildId(plugin, plugins, bundleDeps)
    const bundleRoot = path.resolve(__dirname, 'src/CoreShop/Bundle', plugin.name)
    const studioDir = path.join(bundleRoot, 'Resources/public/studio')
    const outputDir = path.join(studioDir, buildId)

    if (!fs.existsSync(path.join(outputDir, 'entrypoints.json'))) {
      throw new Error(`[${plugin.name}] build output ${outputDir} is missing, cannot package it`)
    }

    fs.writeFileSync(path.join(outputDir, '.build-id'), `${buildId}\n`)

    logPlugin(plugin, 'Packaging build...')
    execSync(`"${packageBin}" --build-dir "${studioDir}" --out-dir "${path.join(bundleRoot, 'Resources/build-dist')}"`, {
      cwd: __dirname,
      stdio: 'inherit'
    })
  }
}

// Color codes for console output
const colors = {
  reset: '\x1b[0m',
  red: '\x1b[31m',
  green: '\x1b[32m',
  yellow: '\x1b[33m',
  blue: '\x1b[34m',
  magenta: '\x1b[35m',
  cyan: '\x1b[36m',
  white: '\x1b[37m'
};

// Auto-discover plugins with studio assets
function discoverPlugins(): Plugin[] {
  const plugins: Plugin[] = [];
  const bundlePath = 'src/CoreShop/Bundle';
  const availableColors = [colors.blue, colors.green, colors.magenta, colors.cyan, colors.yellow, colors.red];
  let colorIndex = 0;
  
  if (!fs.existsSync(bundlePath)) {
    return plugins;
  }
  
  const bundleDirs = fs.readdirSync(bundlePath, { withFileTypes: true })
    .filter((dirent: fs.Dirent) => dirent.isDirectory())
    .map((dirent: fs.Dirent) => dirent.name);
  
  for (const bundleDir of bundleDirs) {
    const studioPath = path.join(bundlePath, bundleDir, 'Resources/assets/pimcore-studio');
    const packageJsonPath = path.join(studioPath, 'package.json');
    
    if (fs.existsSync(studioPath) && fs.existsSync(packageJsonPath)) {
      plugins.push({
        name: bundleDir,
        path: studioPath,
        color: availableColors[colorIndex % availableColors.length]
      });
      colorIndex++;
    }
  }
  
  return plugins.sort((a, b) => a.name.localeCompare(b.name));
}

const plugins = discoverPlugins();

// Fixed port mapping for each bundle to avoid conflicts
const bundlePortMap: Record<string, number> = {
  'address': 3001,
  'core': 3002,
  'currency': 3003,
  'customer': 3004,
  'index': 3005,
  'menu': 3006,
  'messenger': 3007,
  'money': 3008,
  'notification': 3009,
  'order': 3010,
  'payment': 3011,
  'pimcore': 3012,
  'product': 3013,
  'productquantitypricerules': 3014,
  'resource': 3015,
  'rule': 3016,
  'shipping': 3017,
  'store': 3018,
  'taxation': 3019,
  'user': 3020,
  'variant': 3021
};

function errorMessage(error: unknown): string {
  return error instanceof Error ? error.message : String(error)
}

function log(message: string, color: string = colors.reset): void {
  console.log(`${color}${message}${colors.reset}`);
}

function logPlugin(plugin: Plugin, message: string, color: string = plugin.color): void {
  log(`[${plugin.name}] ${message}`, color);
}

async function runCommand(command: string, cwd: string, silent: boolean = false, env?: NodeJS.ProcessEnv): Promise<string> {
  return new Promise<string>((resolve, reject) => {
    try {
      const result = execSync(command, {
        cwd,
        stdio: silent ? 'pipe' : 'inherit',
        encoding: 'utf8',
        env: env || process.env
      });
      resolve(result);
    } catch (error) {
      reject(error);
    }
  });
}

async function checkPluginExists(plugin: Plugin): Promise<boolean> {
  const pluginPath = path.resolve(__dirname, plugin.path);
  const packageJsonPath = path.join(pluginPath, 'package.json');

  if (!fs.existsSync(pluginPath)) {
    logPlugin(plugin, 'Directory does not exist!', colors.red);
    return false;
  }

  if (!fs.existsSync(packageJsonPath)) {
    logPlugin(plugin, 'package.json not found!', colors.red);
    return false;
  }

  return true;
}

async function installDependencies(plugin: Plugin): Promise<void> {
  const pluginPath = path.resolve(__dirname, plugin.path);
  const nodeModulesPath = path.join(pluginPath, 'node_modules');

  // Check if dependencies are already installed
  if (fs.existsSync(nodeModulesPath)) {
    logPlugin(plugin, 'Dependencies already installed, skipping...');
    return;
  }

  logPlugin(plugin, 'Installing dependencies...');
  // await runCommand('npm install', pluginPath);
  logPlugin(plugin, 'Dependencies installed successfully!', colors.green);
}

async function main() {
  const args = process.argv.slice(2);
  const command = args[0] || 'build';
  
  log('🚀 CoreShop Studio Build Script', colors.cyan);
  log(`Command: ${command}`, colors.cyan);
  log('═'.repeat(60), colors.cyan);
  
  if (command === 'install') {
    log('Installing dependencies for all plugins...', colors.yellow);

    for (const plugin of plugins) {
      try {
        if (await checkPluginExists(plugin)) {
          await installDependencies(plugin);
        }
      } catch (error) {
        logPlugin(plugin, `Install failed: ${errorMessage(error)}`, colors.red);
      }
    }
  } else if (command === 'build') {
    const changedOnly = args.includes('--changed-only');
    let buildPlugins = plugins;

    if (changedOnly) {
      const changed = getChangedBundles(plugins);
      if (changed !== null) {
        buildPlugins = changed;
        if (buildPlugins.length === 0) {
          log('No bundles changed, nothing to build.', colors.green);
          process.exit(0);
        }
        log(`Building ${buildPlugins.length} changed bundle(s): ${buildPlugins.map(p => p.name.replace('Bundle', '')).join(', ')}`, colors.yellow);
      } else {
        log('Building all plugins in parallel...', colors.yellow);
      }
    } else {
      log('Building all plugins in parallel...', colors.yellow);
    }

    log(`Found ${buildPlugins.length} bundles to build concurrently`, colors.cyan);

    // Build all plugins in parallel using concurrently for better output management
    const bundleDeps = buildBundleDepsMap(plugins);
    const commands = buildPlugins.map(plugin => {
      const bundleName = plugin.name.replace(/Bundle$/, '').toLowerCase();
      const bundleDir = plugin.name.replace(/Bundle$/, '');
      const buildId = computeBuildId(plugin, plugins, bundleDeps);

      return `CORESHOP_BUNDLE_NAME=${bundleName} CORESHOP_BUNDLE_DIR=${bundleDir} CORESHOP_BUILD_ID=${buildId} rsbuild build --config rsbuild.template.config.ts`;
    });

    const names = buildPlugins.map(plugin => plugin.name.replace('Bundle', '')).join(',');
    const colorNames = ['blue', 'green', 'magenta', 'cyan', 'yellow', 'red']
      .slice(0, Math.min(buildPlugins.length, 6)).join(',');

    // Use concurrently directly (already in devDependencies, no npx overhead)
    const concurrentlyBin = path.resolve(__dirname, 'node_modules/.bin/concurrently');
    const concurrentlyCmd = `${concurrentlyBin} ${commands.map(cmd => `"${cmd}"`).join(' ')} --names "${names}" --prefix-colors "${colorNames}" --kill-others-on-fail --max-processes ${buildPlugins.length}`;

    try {
      await runCommand(concurrentlyCmd, __dirname);
      packageBuilds(buildPlugins, plugins, bundleDeps);
      log('\n🎉 All bundles built successfully!', colors.green);
      process.exit(0);
    } catch (error) {
      log(`\nBuild failed: ${errorMessage(error)}`, colors.red);
      process.exit(1);
    }
  } else if (command === 'dev') {
    const bundleName = args[1];
    
    if (!bundleName) {
      log('Available bundles for dev mode:', colors.yellow);
      plugins.forEach(plugin => {
        log(`  - ${plugin.name}`, colors.white);
      });
      log('\nUsage:', colors.yellow);
      log('  node studio-build.js dev <bundle-name>  - Start single bundle', colors.white);
      log('  node studio-build.js dev all             - Start all bundles', colors.white);
      log('\nExample: node studio-build.js dev ResourceBundle', colors.white);
      process.exit(1);
    }
    
    if (bundleName.toLowerCase() === 'all') {
      // Start all dev servers in parallel using concurrently
      log('Starting all development servers...', colors.yellow);
      log(`Found ${plugins.length} bundles:`, colors.cyan);
      
      const validPlugins: Plugin[] = [];
      for (const plugin of plugins) {
        if (await checkPluginExists(plugin)) {
          await installDependencies(plugin);
          validPlugins.push(plugin);
          logPlugin(plugin, `Ready for dev server`);
        }
      }
      
      if (validPlugins.length === 0) {
        log('No valid bundles found for dev mode!', colors.red);
        process.exit(1);
      }
      
      log('Press Ctrl+C to stop all servers', colors.yellow);
      log('═'.repeat(60), colors.cyan);
      
      // Build concurrently command using template config with fixed ports
      const commands = validPlugins.map(plugin => {
        const bundleName = plugin.name.replace(/Bundle$/, '').toLowerCase();
        const bundleDir = plugin.name.replace(/Bundle$/, '');
        const port = bundlePortMap[bundleName] || 3000;

        return `CORESHOP_BUNDLE_NAME=${bundleName} CORESHOP_BUNDLE_DIR=${bundleDir} CORESHOP_DEV_PORT=${port} NODE_ENV=dev-server rsbuild dev --config rsbuild.template.config.ts`;
      });
      
      const names = validPlugins.map(plugin => plugin.name.replace('Bundle', '')).join(',');
      const colorNames = ['blue', 'green', 'magenta', 'cyan', 'yellow', 'red']
        .slice(0, Math.min(validPlugins.length, 6)).join(',');
      
      // Use concurrently directly (already in devDependencies, no npx overhead)
      const concurrentlyBin = path.resolve(__dirname, 'node_modules/.bin/concurrently');
      const concurrentlyCmd = `${concurrentlyBin} ${commands.map(cmd => `"${cmd}"`).join(' ')} --names "${names}" --prefix-colors "${colorNames}" --kill-others-on-fail --restart-tries 2 --max-processes ${validPlugins.length}`;
      
      try {
        await runCommand(concurrentlyCmd, __dirname);
      } catch (error) {
        log(`Dev servers failed: ${errorMessage(error)}`, colors.red);
        process.exit(1);
      }
      
    } else {
      // Start single bundle
      const plugin = plugins.find(p => p.name.toLowerCase() === bundleName.toLowerCase());
      
      if (!plugin) {
        log(`Bundle "${bundleName}" not found!`, colors.red);
        log('Available bundles:', colors.yellow);
        plugins.forEach(p => log(`  - ${p.name}`, colors.white));
        process.exit(1);
      }
      
      if (!(await checkPluginExists(plugin))) {
        logPlugin(plugin, 'Bundle validation failed!', colors.red);
        process.exit(1);
      }
      
      logPlugin(plugin, 'Starting dev server...', colors.green);
      log('Press Ctrl+C to stop the server', colors.yellow);
      
      try {
        await installDependencies(plugin);
        
        // Use template config for single bundle dev
        const bundleName = plugin.name.replace(/Bundle$/, '').toLowerCase();
        const port = bundlePortMap[bundleName] || 3000;
        
        const devEnv = {
          ...process.env,
          CORESHOP_BUNDLE_NAME: bundleName,
          CORESHOP_BUNDLE_DIR: plugin.name.replace(/Bundle$/, ''),
          CORESHOP_DEV_PORT: port.toString(),
          NODE_ENV: 'dev-server'
        };
        
        await runCommand('rsbuild dev --config rsbuild.template.config.ts', __dirname, false, devEnv);
      } catch (error) {
        logPlugin(plugin, `Dev server failed: ${errorMessage(error)}`, colors.red);
        process.exit(1);
      }
    }
  } else {
    log('Usage:', colors.yellow);
    log('  node studio-build.js [command] [options]', colors.white);
    log('\\nCommands:', colors.yellow);
    log('  install  - Install dependencies for all plugins', colors.white);
    log('  build    - Build all plugins (default)', colors.white);
    log('  dev <bundle-name> - Start development server for specific bundle', colors.white);
    log('  dev all           - Start development servers for all bundles', colors.white);
    log('\\nAvailable bundles for dev:', colors.yellow);
    plugins.forEach(plugin => {
      log(`  - ${plugin.name}`, colors.white);
    });
    process.exit(1);
  }
}

// Handle Ctrl+C gracefully
process.on('SIGINT', () => {
  log('\\n\\n🛑 Build process interrupted', colors.yellow);
  process.exit(130);
});

// Run the script
main().catch((error) => {
  log(`\\n💥 Unexpected error: ${errorMessage(error)}`, colors.red);
  process.exit(1);
});