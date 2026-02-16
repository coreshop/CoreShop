/**
 * CoreShop Form Builder
 *
 * Builder class for composable, decorator-based form configuration.
 * Inspired by Pimcore Studio's ListingBuilder pattern.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import type {
  FormBuilderConfig,
  FormDecorator,
  FormDecoratorContext,
  DecoratorRegistration
} from './types'

/**
 * FormBuilder
 *
 * Manages form configuration with composable decorators.
 * Similar to Pimcore's ListingBuilder pattern.
 *
 * @example
 * ```typescript
 * const builder = new FormBuilder<Country>({
 *   fields: [
 *     { name: 'name', label: 'Name', component: Input }
 *   ]
 * })
 *
 * builder.addDecorator('validation', validationDecorator)
 * builder.addDecorator('sections', sectionDecorator)
 *
 * const config = builder.build(data)
 * ```
 */
export class FormBuilder<T = any> {
  private baseConfig: FormBuilderConfig<T>
  private decorators: Array<DecoratorRegistration<T>> = []

  constructor(baseConfig: FormBuilderConfig<T>) {
    this.baseConfig = baseConfig
  }

  /**
   * Add a decorator
   *
   * Decorators are applied in the order they are added.
   */
  addDecorator(name: string, decorator: FormDecorator<T>): this {
    this.decorators.push({ name, decorator })
    return this
  }

  /**
   * Override an existing decorator
   *
   * If decorator doesn't exist, it will be added.
   */
  overrideDecorator(name: string, decorator: FormDecorator<T>): this {
    const index = this.decorators.findIndex(d => d.name === name)
    if (index >= 0) {
      this.decorators[index] = { name, decorator }
    } else {
      this.addDecorator(name, decorator)
    }
    return this
  }

  /**
   * Get a decorator by name
   */
  getDecorator(name: string): FormDecorator<T> | undefined {
    return this.decorators.find(d => d.name === name)?.decorator
  }

  /**
   * Remove a decorator
   */
  removeDecorator(name: string): this {
    this.decorators = this.decorators.filter(d => d.name !== name)
    return this
  }

  /**
   * Get all decorator names
   */
  getDecoratorNames(): string[] {
    return this.decorators.map(d => d.name)
  }

  /**
   * Copy this builder
   *
   * Creates a new builder with the same config and decorators.
   * Useful for creating variations (e.g., readonly form).
   */
  copy(): FormBuilder<T> {
    const copy = new FormBuilder<T>(this.baseConfig)
    copy.decorators = [...this.decorators]
    return copy
  }

  /**
   * Build final configuration
   *
   * Applies all decorators in order and returns final config.
   */
  build(context?: FormDecoratorContext<T>): FormBuilderConfig<T> {
    let config = { ...this.baseConfig }

    // Apply all decorators in order
    for (const { decorator } of this.decorators) {
      config = decorator(config, context)
    }

    return config
  }

  /**
   * Get base configuration (without decorators)
   */
  getBaseConfig(): FormBuilderConfig<T> {
    return { ...this.baseConfig }
  }
}
