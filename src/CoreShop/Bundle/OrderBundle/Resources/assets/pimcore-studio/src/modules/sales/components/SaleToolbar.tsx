/**
 * CoreShop OrderBundle - Sale Toolbar
 *
 * Renders the toolbar with reload button and dynamically registered buttons
 */

import React from 'react'
import { Button } from 'antd'
import { ReloadOutlined } from '@ant-design/icons'
import { createStyles } from 'antd-style'
import { useSaleContext } from '../context/SaleActionsContext'

export const SaleToolbar: React.FC = () => {
  const { styles } = useToolbarStyles()
  const { buttonRegistry, onReload } = useSaleContext()
  const [, forceUpdate] = React.useReducer(x => x + 1, 0)

  // Set callback to re-render when buttons change
  React.useEffect(() => {
    buttonRegistry.setChangeCallback(forceUpdate)
    return () => buttonRegistry.setChangeCallback(() => {})
  }, [buttonRegistry])

  // Get all registered buttons sorted by priority
  const buttons = buttonRegistry.getAll()

  return (
    <div className={styles.toolbar}>
      <Button
        icon={<ReloadOutlined />}
        onClick={onReload}
        type="default"
      >
        Reload
      </Button>
      {buttons.map(({ key, component: ButtonComponent }) => (
        <ButtonComponent key={key} />
      ))}
    </div>
  )
}

const useToolbarStyles = createStyles(({ css, token }) => ({
  toolbar: css`
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    padding: 12px;
    background: ${token.colorBgContainer};
    border: 1px solid ${token.colorBorderSecondary};
    border-radius: ${token.borderRadius}px;
  `
}))
