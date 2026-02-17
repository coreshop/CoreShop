/**
 * CoreShop OrderBundle Sale Detail
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
import { createStyles } from 'antd-style'
import { container } from '@pimcore/studio-ui-bundle'
import type { Sale, SaleType } from './types'
import type {SaleTabProps, SaleTabRegistry} from './registry'
import { serviceIds } from './service-ids'
import { SaleContextProvider } from './context/SaleActionsContext'
import { SaleToolbar } from './components/SaleToolbar'

interface SaleDetailProps {
  sale: Sale | undefined
  type: SaleType
  onChange: (updates: Partial<Sale>) => void
  onReload?: () => void
}

export const SaleDetail: React.FC<SaleDetailProps> = ({
  sale,
  type,
  onChange,
  onReload = () => {}
}) => {
  const { styles } = useSaleDetailStyles()

  // Get tab registry - memoized to prevent re-fetching
  const tabRegistry = React.useMemo(
    () => container.get<SaleTabRegistry>(serviceIds.saleTabRegistry),
    []
  )

  // Get all blocks for current sale type and group by position
  const blocks = React.useMemo(() => {
    const allBlocks = tabRegistry.getForType(type)

    return {
      all: allBlocks,
      top: allBlocks.filter(b => b.position === 'top').sort((a, b) => a.priority - b.priority),
      left: allBlocks.filter(b => b.position === 'left').sort((a, b) => a.priority - b.priority),
      right: allBlocks.filter(b => b.position === 'right').sort((a, b) => a.priority - b.priority),
      bottom: allBlocks.filter(b => b.position === 'bottom').sort((a, b) => a.priority - b.priority)
    }
  }, [type, tabRegistry])

  if (!sale) {
    return (
      <div className={styles.emptyState}>
        <div className={styles.emptyStateText}>
          Select a {type} to view details
        </div>
      </div>
    )
  }

  const renderBlocks = (blockList: typeof blocks.top) => {
    return blockList.map((block) => {
      const BlockComponent = block.component
      return (
        <div key={block.key} className={styles.block}>
          <BlockComponent />
        </div>
      )
    })
  }

  return (
    <SaleContextProvider
      sale={sale ?? null}
      onChange={onChange}
      onReload={onReload}
    >
      <div className={styles.container}>
        {/* Toolbar with dynamically registered buttons */}
        <SaleToolbar />

      {/* Top Area */}
      {blocks.top.length > 0 && (
        <div className={styles.topArea}>
          {renderBlocks(blocks.top)}
        </div>
      )}

      {/* Two Column Area */}
      <div className={styles.columnsArea}>
        {/* Left Column (flex: 7) */}
        <div className={styles.leftColumn}>
          {renderBlocks(blocks.left)}
        </div>

        {/* Right Column (flex: 5) */}
        <div className={styles.rightColumn}>
          {renderBlocks(blocks.right)}
        </div>
      </div>

      {/* Bottom Area */}
      {blocks.bottom.length > 0 && (
        <div className={styles.bottomArea}>
          {renderBlocks(blocks.bottom)}
        </div>
      )}
      </div>
    </SaleContextProvider>
  )
}

const useSaleDetailStyles = createStyles(({ css, token }) => ({
  container: css`
    container-type: inline-size;
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: auto;
    padding: 8px 12px;
    background: ${token.colorBgLayout};
    gap: 16px;
  `,
  topArea: css`
    margin-bottom: 0;
  `,
  columnsArea: css`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex-direction: row;
    }
  `,
  leftColumn: css`
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex: 1 1 50%;
    }
  `,
  rightColumn: css`
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex: 1 1 50%;
    }
  `,
  bottomArea: css`
    margin-bottom: 0;
  `,
  block: css`
    .ant-card {
      border-radius: ${token.borderRadiusLG}px;
      border: 1px solid ${token.colorBorderSecondary};
      box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03), 0 1px 6px -1px rgba(0, 0, 0, 0.02);
      overflow: hidden;

      .ant-card-head {
        border-bottom: 1px solid ${token.colorBorderSecondary};
        min-height: 44px;
        padding: 0 16px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.01em;
        text-transform: uppercase;
        color: ${token.colorTextSecondary};
      }

      .ant-card-head-title {
        font-size: 13px;
        padding: 10px 0;
      }

      .ant-card-extra {
        padding: 6px 0;
      }
    }
  `,
  emptyState: css`
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    min-height: 400px;
  `,
  emptyStateText: css`
    color: ${token.colorTextTertiary};
    font-size: 14px;
  `
}))
