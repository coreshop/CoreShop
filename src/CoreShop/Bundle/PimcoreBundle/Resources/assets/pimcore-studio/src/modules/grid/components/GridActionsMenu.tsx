/**
 * CoreShop PimcoreBundle Grid Actions Menu
 *
 * Context menu component for grid actions.
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
import { Menu, Modal, Spin } from 'antd'
import { ExclamationCircleOutlined, FolderOpenOutlined, ThunderboltOutlined } from '@ant-design/icons'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { renderApiError } from '@coreshop/resource/src/entities'
import { useTranslation } from 'react-i18next'
import type { MenuProps } from 'antd'
import { useGridActions } from '../hooks'
import { coreshopBroker } from '../../broker'
import { GRID_EVENTS } from '../events'

interface GridActionsMenuProps {
  listType: string
  selectedIds: number[]
  onActionComplete: () => void
  openHandler: (id: number) => void
  onClose?: () => void
}

type MenuItem = Required<MenuProps>['items'][number]

/**
 * Context menu for grid actions
 *
 * Displays:
 * - Open / Open Selected (for multiple selections)
 * - Divider
 * - Registered actions
 *
 * @example
 * <GridActionsMenu
 *   listType="coreshop_order"
 *   selectedIds={[1, 2, 3]}
 *   onActionComplete={handleRefresh}
 *   openHandler={(id) => openOrderDetail(id)}
 * />
 */
export const GridActionsMenu: React.FC<GridActionsMenuProps> = ({
  listType,
  selectedIds,
  onActionComplete,
  openHandler,
  onClose
}) => {
  const { t } = useTranslation()
  const { actions, loading, executing, executeAction } = useGridActions(listType)
  const [modal, contextHolder] = Modal.useModal()
  const messageApi = useMessage()

  const handleOpenSelected = (): void => {
    selectedIds.forEach(id => openHandler(id))
    onClose?.()
  }

  const handleExecuteAction = (actionId: string, actionName: string): void => {
    modal.confirm({
      title: t('coreshop_grid_action_confirm', { defaultValue: 'Confirm Action' }),
      icon: <ExclamationCircleOutlined />,
      content: t('coreshop_grid_action_confirm_message', {
        defaultValue: 'Apply "%{action}" to %{count} item(s)?',
        action: actionName,
        count: selectedIds.length
      }),
      okText: t('coreshop_grid_action_execute', { defaultValue: 'Execute' }),
      cancelText: t('coreshop_grid_action_cancel', { defaultValue: 'Cancel' }),
      onOk: async () => {
        const result = await executeAction(actionId, selectedIds)

        // Fire broker event
        coreshopBroker.fireEvent(GRID_EVENTS.ACTION_EXECUTED, {
          listType,
          actionId,
          ids: selectedIds,
          success: result.success,
          message: result.message
        })

        if (result.success) {
          void messageApi.success(result.message || t('coreshop_grid_action_success', { defaultValue: 'Action completed successfully' }))
          onActionComplete()
        } else {
          void messageApi.error(renderApiError(result.message || t('coreshop_grid_action_error', { defaultValue: 'Action failed' })))
        }

        onClose?.()
      }
    })
  }

  // Build menu items
  const menuItems: MenuItem[] = []

  // Fire event to allow extensions to add items
  const extensionItems: any[] = []
  coreshopBroker.fireEvent(GRID_EVENTS.CONTEXT_MENU_BUILDING, {
    listType,
    selectedIds,
    menuItems: extensionItems
  })

  // Open action(s)
  if (selectedIds.length === 1) {
    menuItems.push({
      key: 'open',
      icon: <FolderOpenOutlined />,
      label: t('coreshop_grid_open', { defaultValue: 'Open' }),
      onClick: handleOpenSelected
    })
  } else if (selectedIds.length > 1) {
    menuItems.push({
      key: 'open-selected',
      icon: <FolderOpenOutlined />,
      label: t('coreshop_grid_open_selected', {
        defaultValue: 'Open Selected (%{count})',
        count: selectedIds.length
      }),
      onClick: handleOpenSelected
    })
  }

  // Add extension items if any
  if (extensionItems.length > 0) {
    menuItems.push(...extensionItems)
  }

  // Divider and actions submenu
  if (actions.length > 0 && selectedIds.length > 0) {
    menuItems.push({ type: 'divider', key: 'divider-1' })

    menuItems.push({
      key: 'actions',
      icon: <ThunderboltOutlined />,
      label: t('coreshop_grid_actions', {
        defaultValue: 'Actions (%{count})',
        count: actions.length
      }),
      children: actions.map(action => ({
        key: `action-${action.id}`,
        label: action.name,
        onClick: () => handleExecuteAction(action.id, action.name)
      }))
    })
  }

  if (loading) {
    return <Spin size="small" />
  }

  return (
    <>
      {contextHolder}
      <Menu
        items={menuItems}
        style={{ minWidth: 200 }}
        selectable={false}
      />
    </>
  )
}
