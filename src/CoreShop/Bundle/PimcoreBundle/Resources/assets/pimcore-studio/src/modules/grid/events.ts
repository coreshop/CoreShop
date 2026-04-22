/**
 * CoreShop PimcoreBundle Grid Events
 *
 * Event constants for the grid filter and action system.
 * These events are fired through the coreshopBroker for extensibility.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

/**
 * Grid system events
 *
 * Usage with broker:
 *   import { coreshopBroker } from '@coreshop/pimcore/src/modules/broker'
 *   import { GRID_EVENTS } from '@coreshop/pimcore/src/modules/grid'
 *
 *   // Listen for filter changes
 *   coreshopBroker.addListener(GRID_EVENTS.FILTER_CHANGED, (data) => {
 *     console.log('Filter changed:', data.listType, data.filterId)
 *   })
 *
 *   // Listen for action execution
 *   coreshopBroker.addListener(GRID_EVENTS.ACTION_EXECUTED, (data) => {
 *     console.log('Action executed:', data.actionId, data.success)
 *   })
 */
export const GRID_EVENTS = {
  /**
   * Fired when a grid filter is changed
   * Payload: { listType: string, filterId: string | null }
   */
  FILTER_CHANGED: 'coreshop.grid.filter.changed',

  /**
   * Fired after a grid action is executed
   * Payload: { listType: string, actionId: string, ids: number[], success: boolean, message: string }
   */
  ACTION_EXECUTED: 'coreshop.grid.action.executed',

  /**
   * Fired before toolbar is enhanced (allows adding custom elements)
   * Payload: { listType: string, toolbarItems: any[] }
   */
  TOOLBAR_ENHANCING: 'coreshop.grid.toolbar.enhancing',

  /**
   * Fired before context menu is built (allows adding custom items)
   * Payload: { listType: string, selectedIds: number[], menuItems: any[] }
   */
  CONTEXT_MENU_BUILDING: 'coreshop.grid.contextmenu.building'
} as const

export type GridEventType = typeof GRID_EVENTS[keyof typeof GRID_EVENTS]
