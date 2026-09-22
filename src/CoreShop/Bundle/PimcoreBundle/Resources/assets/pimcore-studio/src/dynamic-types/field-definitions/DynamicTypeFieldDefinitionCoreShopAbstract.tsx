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

import React from 'react'
import {
  DynamicTypeFieldDefinitionDataAbstract,
  type FieldDefinitionContext
} from '@pimcore/studio-ui-bundle/modules/field-definitions'
import type { ElementIcon } from '@pimcore/studio-ui-bundle/modules/widget-manager'

/**
 * Group key used for all CoreShop field types in the class editor "add field" dropdown.
 * The group itself is registered once via registerCoreShopFieldDefinitionGroup().
 */
export const CORESHOP_FIELD_DEFINITION_GROUP = 'coreshop'

/**
 * Base class for all CoreShop class-definition field types.
 *
 * Provides the shared "data/coreshop" dropdown group, a default icon and
 * hides the "unique" switch (CoreShop types never support unique constraints).
 * Subclasses only need to set the id and, if the PHP CoreExtension exposes
 * type-specific options, override getSpecificFormFields().
 */
export abstract class DynamicTypeFieldDefinitionCoreShopAbstract extends DynamicTypeFieldDefinitionDataAbstract {
  getIcon(): ElementIcon {
    return { type: 'name', value: 'coreshop_field_definition_group' }
  }

  getGroup(): string[] {
    return [...super.getGroup(), CORESHOP_FIELD_DEFINITION_GROUP]
  }

  getFormFields(context: FieldDefinitionContext): React.JSX.Element {
    return super.getFormFields({ ...context, hideUnique: true })
  }
}
