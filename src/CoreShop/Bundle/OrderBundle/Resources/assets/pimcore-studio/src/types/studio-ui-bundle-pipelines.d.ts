export {}

declare module '@pimcore/studio-ui-bundle/modules/element' {
  import type { ReactElement } from 'react'

  export abstract class DynamicTypePipelineAbstract {
    abstract readonly id: string
    readonly group: string | string[] | null
    abstract getComponent(): ReactElement
    isAvailableForSelection(config: Record<string, any>): boolean
  }

  export class DynamicTypePipelineRegistry {
    registerDynamicType(type: DynamicTypePipelineAbstract): void
    getDynamicType(id: string, throwException?: boolean): DynamicTypePipelineAbstract
    getDynamicTypes(): DynamicTypePipelineAbstract[]
    overrideDynamicType(type: DynamicTypePipelineAbstract): void
    hasDynamicType(id: string): boolean
  }
}
