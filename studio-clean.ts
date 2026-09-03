/**
 * Removes all generated Studio build output (src/CoreShop/Bundle/<X>Bundle/Resources/public/studio/*).
 *
 * The expanded builds are not tracked in git: each bundle ships its build as an archive in
 * Resources/build-dist, from which Pimcore extracts it again at cache warmup. Run
 * `npm run build` afterwards to get a Studio built from the local sources.
 *
 * Usage:
 *   npm run clean:builds            # delete
 *   npm run clean:builds -- --dry   # list what would be deleted
 */

import fs from 'fs'
import path from 'path'

const bundlesRoot = path.resolve(__dirname, 'src/CoreShop/Bundle')
const dryRun = process.argv.slice(2).includes('--dry')

function studioDirs(): string[] {
  if (!fs.existsSync(bundlesRoot)) {
    return []
  }

  return fs.readdirSync(bundlesRoot, { withFileTypes: true })
    .filter(entry => entry.isDirectory() && entry.name.endsWith('Bundle'))
    .map(entry => path.join(bundlesRoot, entry.name, 'Resources/public/studio'))
    .filter(dir => fs.existsSync(dir))
}

function countFiles(dir: string): number {
  return fs.readdirSync(dir, { withFileTypes: true, recursive: true })
    .filter(entry => entry.isFile())
    .length
}

let removedBuilds = 0
let removedFiles = 0

for (const studioDir of studioDirs()) {
  for (const build of fs.readdirSync(studioDir, { withFileTypes: true })) {
    if (!build.isDirectory()) {
      continue
    }

    const buildDir = path.join(studioDir, build.name)
    const files = countFiles(buildDir)
    const relative = path.relative(__dirname, buildDir)

    if (dryRun) {
      console.log(`would remove ${relative} (${files} files)`)
    } else {
      fs.rmSync(buildDir, { recursive: true, force: true })
      console.log(`removed ${relative} (${files} files)`)
    }

    removedBuilds++
    removedFiles += files
  }
}

console.log(
  removedBuilds === 0
    ? 'No studio build output found.'
    : `${dryRun ? 'Would remove' : 'Removed'} ${removedBuilds} build director${removedBuilds === 1 ? 'y' : 'ies'} (${removedFiles} files).`
)
