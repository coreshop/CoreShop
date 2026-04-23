import { container } from '@pimcore/studio-ui-bundle'

export const entitySaveDecoratorsServiceId = 'CoreShop/Studio/EntitySaveDecorators'

export type SaveDecorator<T = any> = (payload: Record<string, any>, data?: T) => Record<string, any>

export class EntitySaveDecoratorRegistry {
  private map = new Map<string, SaveDecorator[]>()

  add(resourceKey: string, decorator: SaveDecorator): void {
    const list = this.map.get(resourceKey) ?? []
    list.push(decorator)
    this.map.set(resourceKey, list)
  }

  apply(resourceKey: string, payload: Record<string, any>, data?: any): Record<string, any> {
    const list = this.map.get(resourceKey) ?? []
    return list.reduce((acc, d) => d(acc, data), payload)
  }
}

export function getEntitySaveDecoratorRegistry(): EntitySaveDecoratorRegistry | undefined {
  try {
    return container.get<EntitySaveDecoratorRegistry>(entitySaveDecoratorsServiceId)
  } catch (e) {
    return undefined
  }
}

