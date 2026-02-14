/**
 * Notification Rules Extension Module
 *
 * All notification rule conditions are now schema-generated from backend form types.
 * This module is kept as a placeholder for any future non-schema extensions.
 */

import { type AbstractModule } from '@pimcore/studio-ui-bundle'

export const NotificationRulesExtensionModule: AbstractModule = {
  onInit(): void {
    // All notification conditions/actions are now auto-registered from backend schemas
    // via registerSchemaComponentsFromMaps() in NotificationRuleManager.
  }
}
