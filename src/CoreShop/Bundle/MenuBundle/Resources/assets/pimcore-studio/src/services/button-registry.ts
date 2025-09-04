/**
 * CoreShop Menu Bundle - Pimcore Studio Plugin
 *
 * Main entry point that registers the CoreShop Menu Extension module
 * following the Pimcore Studio plugin pattern
 */


import { injectable } from 'inversify'
import { type ButtonConfig } from '../types/ButtonConfig'

@injectable()
export class MenuButtonRegistry {
  private readonly items: ButtonConfig[] = []

  add (item: ButtonConfig): void {
    this.items.push(item)
  }

  get (name: string): ButtonConfig | undefined {
    return this.items.find((item) => item.name === name)
  }

  all (): ButtonConfig[] {
    return this.items
  }
}
