/**
 * CoreShop OrderBundle Sale Detail Skeleton
 *
 * Skeleton loader that mimics the SaleDetail layout while data is loading.
 */

import React from 'react'
import { Skeleton, Card } from 'antd'
import { createStyles } from 'antd-style'

export const SaleDetailSkeleton: React.FC = () => {
  const { styles } = useSkeletonStyles()

  return (
    <div className={styles.container}>
      {/* Toolbar */}
      <div className={styles.toolbar}>
        <Skeleton.Button active size="small" style={{ width: 80, height: 28 }} />
        <div style={{ flex: 1 }} />
        <Skeleton.Button active size="small" style={{ width: 100, height: 28 }} />
        <Skeleton.Button active size="small" style={{ width: 100, height: 28 }} />
      </div>

      {/* Header: 4 state cards */}
      <div className={styles.statesRow}>
        {[1, 2, 3, 4].map(i => (
          <div key={i} className={styles.stateCard}>
            <Skeleton.Button active size="small" style={{ width: 50, height: 12 }} />
            <Skeleton.Button active style={{ width: 80, height: 24, borderRadius: 999 }} />
          </div>
        ))}
      </div>

      {/* Metrics: 4 cards */}
      <div className={styles.statesRow}>
        {[1, 2, 3, 4].map(i => (
          <div key={i} className={styles.metricCard}>
            <Skeleton.Button active size="small" style={{ width: 40, height: 10 }} />
            <Skeleton.Button active style={{ width: 100, height: 20 }} />
          </div>
        ))}
      </div>

      {/* Two column layout */}
      <div className={styles.columnsArea}>
        {/* Left column */}
        <div className={styles.column}>
          {/* Order info card */}
          <Card className={styles.card}>
            <Skeleton active paragraph={{ rows: 3 }} />
          </Card>

          {/* Payments card */}
          <Card className={styles.card}>
            <Skeleton active paragraph={{ rows: 2 }} />
          </Card>

          {/* Shipments card */}
          <Card className={styles.card}>
            <Skeleton active paragraph={{ rows: 2 }} />
          </Card>
        </div>

        {/* Right column */}
        <div className={styles.column}>
          {/* Customer card */}
          <Card className={styles.card}>
            <div className={styles.customerSkeleton}>
              <Skeleton.Avatar active size={44} />
              <Skeleton active title={{ width: 160 }} paragraph={{ rows: 1, width: [200] }} />
            </div>
          </Card>

          {/* Comments card */}
          <Card className={styles.card}>
            <Skeleton active paragraph={{ rows: 2 }} />
          </Card>
        </div>
      </div>

      {/* Products card */}
      <Card className={styles.card}>
        <Skeleton active paragraph={{ rows: 3 }} />
      </Card>
    </div>
  )
}

const useSkeletonStyles = createStyles(({ css, token }) => ({
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
  toolbar: css`
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid ${token.colorBorderSecondary};
  `,
  statesRow: css`
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;

    @container (min-width: 600px) {
      grid-template-columns: repeat(4, 1fr);
    }
  `,
  stateCard: css`
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 14px 12px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid ${token.colorBorderSecondary};
  `,
  metricCard: css`
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 16px;
    background: ${token.colorBgContainer};
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid ${token.colorBorderSecondary};
  `,
  columnsArea: css`
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex-direction: row;
    }
  `,
  column: css`
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;

    @container (min-width: 800px) {
      flex: 1 1 50%;
    }
  `,
  card: css`
    border-radius: ${token.borderRadiusLG}px;
    border: 1px solid ${token.colorBorderSecondary};
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
  `,
  customerSkeleton: css`
    display: flex;
    align-items: center;
    gap: 14px;
  `
}))
