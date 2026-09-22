/**
 * CoreShop PimcoreBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import type {
  DynamicTypeFieldDefinitionAbstract,
  DynamicTypeFieldDefinitionRegistry
} from '@pimcore/studio-ui-bundle/modules/field-definitions'
import { CORESHOP_FIELD_DEFINITION_GROUP } from './DynamicTypeFieldDefinitionCoreShopAbstract'

/**
 * Registers the "CoreShop" group in the class editor's "add field" dropdown.
 * Safe to call from every bundle; the group is only registered once.
 */
export const registerCoreShopFieldDefinitionGroup = (): void => {
  const registry = container.get<DynamicTypeFieldDefinitionRegistry>(
    serviceIds['DynamicTypes/FieldDefinitionRegistry']
  )
  const groupKey = `data/${CORESHOP_FIELD_DEFINITION_GROUP}`

  // Every CoreShop bundle calls this; the registry logs an error on duplicate keys.
  if (groupKey in registry.getDropdownGroupInfos()) {
    return
  }

  registry.registerDropdownGroupInfo(groupKey, {
    icon: { type: 'name', value: 'coreshop_field_definition_group' },
    translationKey: 'field-definition.groups.data.coreshop',
    order: 950
  })
}

/**
 * Registers CoreShop class-definition field types in Pimcore's FieldDefinitionRegistry
 * so they can be added and configured in the Studio class editor.
 *
 * Throws when the registry is not bound (e.g. inside the reduced document editor
 * iframe); callers wrap the call in the same try/catch they use for the
 * ObjectDataRegistry.
 */
export const registerCoreShopFieldDefinitionTypes = (types: DynamicTypeFieldDefinitionAbstract[]): void => {
  registerCoreShopFieldDefinitionGroup()

  const registry = container.get<DynamicTypeFieldDefinitionRegistry>(
    serviceIds['DynamicTypes/FieldDefinitionRegistry']
  )

  types.forEach(type => {
    if (!registry.hasDynamicType(type.id)) {
      registry.registerDynamicType(type)
    }
  })
}
