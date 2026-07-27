import { createStyles } from 'antd-style'

export const useEntityListStyles = createStyles(({ token, css }) => ({
  tree: css`
    padding: ${token.paddingXS}px;
    background: transparent;

    .ant-tree-treenode {
      display: flex;
      align-items: center;
      width: 100%;
      padding: 1px 0;
    }

    .ant-tree-node-content-wrapper {
      flex: 1 1 auto;
      width: auto;
      white-space: nowrap;
      display: flex;
      align-items: center;
      border-radius: ${token.borderRadiusSM}px;
      transition: background-color 0.2s;
      padding: 3px 6px;
      line-height: 22px;
    }

    .ant-tree-title {
      display: inline-flex;
      align-items: center;
      overflow: hidden;
      text-overflow: ellipsis;
      width: 100%;
    }

    .ant-tree-switcher {
      width: 18px;
      line-height: 22px;
    }

    @media (hover: hover) {
      .ant-tree-node-content-wrapper:hover {
        background-color: ${token.colorFillQuaternary};
      }
    }

    .ant-tree-node-content-wrapper:focus {
      outline: none;
      background-color: ${token.colorFillQuaternary};
    }

    .ant-tree-node-content-wrapper.ant-tree-node-selected {
      background-color: ${token.colorPrimaryBg};
    }
  `,

  droppableInline: css`
    display: inline-flex;
    align-items: center;
    gap: 8px;
  `,

  contentPadding: css`
    padding: ${token.paddingSM}px;
  `,

  leafNode: css`
    display: inline-flex;
    align-items: center;
    gap: 6px;
  `,

  leafIcon: css`
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
    color: ${token.colorTextTertiary};
  `,

  groupNode: css`
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
  `,

  groupCount: css`
    font-size: 11px;
    color: ${token.colorTextQuaternary};
    font-weight: 400;
  `,

  inactive: css`
    color: ${token.colorTextDisabled};
    text-decoration: line-through;
  `,

  inactiveTag: css`
    margin-left: 4px;
    font-size: ${token.fontSizeSM - 1}px;
    line-height: ${token.fontSizeSM + 4}px;
    padding: 0 4px;
    border-radius: ${token.borderRadiusSM}px;
  `,
}))
