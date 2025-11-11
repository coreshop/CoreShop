/**
 * CoreShop OrderBundle - Component Registry
 *
 * Allows CoreBundle to override/extend components like CreateShipmentModal
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import type { CreateShipmentModalProps } from '../components'

interface ComponentRegistry {
  CreateShipmentModal: React.ComponentType<CreateShipmentModalProps>
}

let registry: Partial<ComponentRegistry> = {}

export const registerComponent = <K extends keyof ComponentRegistry>(
  key: K,
  component: ComponentRegistry[K]
): void => {
  registry[key] = component
}

export const getComponent = <K extends keyof ComponentRegistry>(
  key: K,
  fallback: ComponentRegistry[K]
): ComponentRegistry[K] => {
  return (registry[key] || fallback) as ComponentRegistry[K]
}

export const hasComponent = <K extends keyof ComponentRegistry>(key: K): boolean => {
  return registry[key] !== undefined
}
