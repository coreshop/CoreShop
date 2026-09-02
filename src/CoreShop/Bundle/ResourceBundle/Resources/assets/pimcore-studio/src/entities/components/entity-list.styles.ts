import { createStyles } from 'antd-style'

export const useEntityListStyles = createStyles(({ token, css }) => ({
  // Pimcore's Draggable/Droppable render a block-level div inside .ant-tree-title, which would
  // push the label off the icon's line box. Pimcore fixes this the same way for its own drag
  // wrappers (.hotspot-droppable .ant-tree-title__btn { height: 24px }).
  tree: css`
    padding: ${token.paddingXS}px 0;

    .ant-tree-title__btn {
      height: 24px;
    }
  `,

  contentPadding: css`
    padding: ${token.paddingSM}px;
  `,

  inactive: css`
    .ant-tree-title__btn {
      color: ${token.colorTextDisabled};
    }
  `,
}))
