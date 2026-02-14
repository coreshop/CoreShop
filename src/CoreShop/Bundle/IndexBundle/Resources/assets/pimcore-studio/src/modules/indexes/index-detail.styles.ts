import { createStyles } from 'antd-style'

export const useIndexDetailStyles = createStyles(({ css }) => ({
  root: css`
    height: 100%;
    display: flex;
    flex-direction: column;

    .ant-tabs {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 0;
    }

    .ant-tabs-content-holder {
      flex: 1;
      min-height: 0;
      overflow: hidden;
    }

    .ant-tabs-content {
      height: 100%;
      min-height: 0;
    }

    .ant-tabs-tabpane {
      height: 100%;
      min-height: 0;
      overflow: auto;
    }
  `
}))
