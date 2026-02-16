/**
 * CoreShop About Modal
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Spin } from 'antd'
import { Button, Flex, type IWindowModalProps, Modal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { createStyles } from 'antd-style'
import { getErrorMessage } from '@coreshop/resource/src/entities'

const useStyles = createStyles(({ css, token }) => ({
  modal: css`
    .ant-modal-content {
      border-radius: ${token.borderRadiusSM}px;

      .ant-modal-body {
        padding: 24px;
      }
    }
  `,
  content: css`
    display: flex;
    flex-direction: column;
    align-items: center;
  `,
  logo: css`
    margin-bottom: 16px;

    img {
      height: 80px;
      width: auto;
    }
  `,
  version: css`
    font-size: 14px;
    color: ${token.colorTextSecondary};
    margin-bottom: 12px;
  `,
  copyright: css`
    font-size: 13px;
    color: ${token.colorTextSecondary};
    margin-bottom: 8px;
  `,
  linkBtn: css`
    padding: 0 !important;
  `,
  loading: css`
    display: flex;
    justify-content: center;
    padding: 40px;
  `
}))

export interface AboutModalProps extends Omit<IWindowModalProps, 'children'> {}

interface SettingsResponse {
  bundle: {
    version: string
  }
}

export const AboutModal: React.FC<AboutModalProps> = (props) => {
  const { t } = useTranslation()
  const { styles } = useStyles()
  const messageApi = useMessage()
  const [version, setVersion] = React.useState<string | null>(null)
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    if (props.open && !version) {
      const fetchVersion = async (): Promise<void> => {
        try {
          const response = await fetch('/pimcore-studio/api/coreshop/settings/get-settings')
          const data: SettingsResponse = await response.json()
          setVersion(data.bundle.version)
        } catch (error) {
          void messageApi.error(getErrorMessage(error, 'Failed to fetch CoreShop version'))
          setVersion('Unknown')
        } finally {
          setLoading(false)
        }
      }
      void fetchVersion()
    }
  }, [props.open, version])

  return (
    <Modal
      {...props}
      className={styles.modal}
      footer={<></>}
      title={t('coreshop_about', { defaultValue: 'About CoreShop' })}
      width={450}
    >
      {loading ? (
        <div className={styles.loading}>
          <Spin size="large" />
        </div>
      ) : (
        <div className={styles.content}>
          <div className={styles.logo}>
            <img
              src="/bundles/coreshopcore/pimcore/img/logo-full.svg"
              alt="CoreShop"
            />
          </div>

          <span className={styles.version}>
            Version: {version}
          </span>

          <Flex align="center" gap="mini">
            <span className={styles.copyright}>
              © by CoreShop GmbH, Wels, Austria
            </span>
            <span>(</span>
            <Button
              className={styles.linkBtn}
              href="https://www.coreshop.org"
              target="_blank"
              type="link"
            >
              coreshop.org
            </Button>
            <span>)</span>
          </Flex>

          <Flex gap="normal" style={{ marginTop: 8 }}>
            <Button
              href="https://github.com/coreshop/CoreShop/blob/master/LICENSE.md"
              target="_blank"
              type="link"
            >
              {t('coreshop_license', { defaultValue: 'License' })}
            </Button>

            <Button
              href="https://www.coreshop.org/contact"
              target="_blank"
              type="link"
            >
              {t('coreshop_contact', { defaultValue: 'Contact' })}
            </Button>
          </Flex>
        </div>
      )}
    </Modal>
  )
}
