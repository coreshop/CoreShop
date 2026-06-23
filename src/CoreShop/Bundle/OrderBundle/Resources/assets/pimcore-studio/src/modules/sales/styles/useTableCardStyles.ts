/**
 * Shared styles for sale detail table cards (Payments, Shipments, Invoices)
 */

import { createStyles } from 'antd-style'

export const useTableCardStyles = createStyles(({ css, token }) => ({
  card: css`
    .ant-card-body {
      padding: 0;
    }

    .ant-empty {
      padding: 24px 16px;
      margin: 0;
      margin-block: 0;
    }
  `,
  table: css`
    .ant-table-thead > tr > th {
      background: ${token.colorBgLayout};
      font-weight: 600;
      font-size: 12px;
      color: ${token.colorTextSecondary};
      padding: 8px 16px !important;
      white-space: nowrap;
    }

    .ant-table-thead > tr > th:first-child {
      padding-left: 24px !important;
    }

    .ant-table-thead > tr > th:last-child {
      padding-right: 24px !important;
    }

    .ant-table-tbody > tr > td {
      padding: 10px 16px !important;
    }

    .ant-table-tbody > tr > td:first-child {
      padding-left: 24px !important;
    }

    .ant-table-tbody > tr > td:last-child {
      padding-right: 24px !important;
    }

    .ant-table-tbody > tr:hover > td {
      background: ${token.colorPrimaryBg} !important;
    }
  `,
  dimText: css`
    color: ${token.colorTextSecondary};
    font-size: 12px;
  `,
  statusBadge: css`
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    color: #fff;
    white-space: nowrap;
    line-height: 1.5;
  `,
  statusBadgeClickable: css`
    cursor: pointer;
    transition: opacity 0.15s;

    &:hover {
      opacity: 0.85;
    }
  `
}))
