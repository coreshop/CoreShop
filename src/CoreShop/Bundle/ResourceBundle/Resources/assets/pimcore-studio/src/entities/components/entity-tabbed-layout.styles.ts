import { createStyles } from 'antd-style'

export const useEntityTabbedLayoutStyles = createStyles(({ token, css }) => ({
  contentPadding: css`
    padding: ${token.paddingSM}px;
  `,
  detailContent: css`
    overflow: auto;
  `,
}))

