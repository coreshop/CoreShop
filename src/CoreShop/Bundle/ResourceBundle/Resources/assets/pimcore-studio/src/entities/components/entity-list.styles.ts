import { createStyles } from 'antd-style'

export const useEntityListStyles = createStyles(({ token, css }) => ({
  tree: css`
    /* make each treenode a flex row so content can stretch */
    .ant-tree-treenode {
      display: flex;
      align-items: center;
      width: 100%;
    }

    .ant-tree-node-content-wrapper {
      flex: 1 1 auto;
      width: auto;
      white-space: nowrap;
      display: flex;
      align-items: center;
    }

    .ant-tree-title {
      display: inline-block;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    @media (hover: hover) {
      .ant-tree-node-content-wrapper:hover {
        background-color: ${token.controlItemBgActiveHover};
      }
    }

    .ant-tree-node-content-wrapper:focus {
      outline: none;
      background-color: ${token.controlItemBgActiveHover};
    }

    .ant-tree-node-selected > .ant-tree-node-content-wrapper {
      background-color: ${token.controlItemBgActive};
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
}))
