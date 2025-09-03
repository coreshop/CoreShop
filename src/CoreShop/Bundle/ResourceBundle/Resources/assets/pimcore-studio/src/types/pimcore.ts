/**
 * Mock Pimcore Studio UI types
 * These would normally come from @pimcore/studio-ui-bundle
 */

export interface PluginDefinition {
  name: string
  version: string
  onInit?: () => void
  onStartup?: () => void
}

// Mock Pimcore Studio UI exports
export const mockPimcoreStudio = {
  registerPlugin: (plugin: PluginDefinition) => {
    console.log(`[Mock Pimcore Studio] Registering plugin: ${plugin.name}`)
  }
}