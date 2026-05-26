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
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

interface InlineLogoProps {
  className?: string
}

const ICON_PATH = 'M47.94,50.58,37.37,56.69,13.68,43V29.82L37.37,16.15l31.86,18.4V25.1L37.37,6.7,5.5,25.1V47.73l31.87,18.4L56.12,55.3V45.86l-8.18-4.72v9.44ZM26.79,36.42l10.58-6.11L61.05,44V57.18L37.37,70.85,5.5,52.45V61.9L37.37,80.3,69.23,61.9V39.27L37.37,20.87,18.62,31.7v9.44l8.17,4.72V36.42Z'
const TEXT_PATH = 'M101.94,58.13a14.6,14.6,0,0,0,11.48-5l-3.19-3.24c-2.43,2.27-4.65,3.68-8.12,3.68-5.42,0-9.34-4.53-9.34-10.06v-.08c0-5.54,3.92-10,9.34-10,3.23,0,5.65,1.41,7.91,3.51l3.2-3.67a14.74,14.74,0,0,0-11.07-4.41A14.32,14.32,0,0,0,87.56,43.5v.08a14.23,14.23,0,0,0,14.38,14.55Zm30.43,0A14.49,14.49,0,0,0,147.2,43.5v-.08a14.79,14.79,0,0,0-29.58.08v.08a14.36,14.36,0,0,0,14.75,14.55Zm.08-4.57c-5.61,0-9.61-4.57-9.61-10.06v-.08c0-5.5,3.92-10,9.53-10S142,38,142,43.5v.08c0,5.5-3.92,10-9.54,10Zm21.82,4.08h5V47.78h6.22l6.95,9.86h5.86l-7.63-10.71c4-1.13,6.74-3.91,6.74-8.56v-.08a8.55,8.55,0,0,0-2.34-6.1c-1.82-1.78-4.61-2.83-8.16-2.83H154.27V57.64Zm5-14.26v-9.5h7.23c3.68,0,5.86,1.66,5.86,4.69v.08c0,2.87-2.26,4.73-5.82,4.73Zm25.3,14.26h21.17V53.2h-16.2V45.6h14.18V41.16H189.51V33.8h16V29.36h-21V57.64Zm38.18.4c5.62,0,9.66-3.15,9.66-8V50c0-4.32-2.91-6.62-9.21-8s-7.68-2.82-7.68-5.49v-.08c0-2.55,2.34-4.57,6-4.57a12.41,12.41,0,0,1,8.16,3.07l1.86-2.46A14.77,14.77,0,0,0,221.59,29c-5.37,0-9.29,3.27-9.29,7.71v.08c0,4.65,3,6.75,9.49,8.16,5.94,1.26,7.36,2.75,7.36,5.38v.08c0,2.79-2.51,4.81-6.31,4.81s-6.74-1.34-9.69-4l-2,2.34A16.53,16.53,0,0,0,222.72,58Zm17.22-.4h3.19V44.91h16.32V57.64h3.19V29.36h-3.19V41.92H243.13V29.36h-3.19V57.64Zm44.89.49A14.33,14.33,0,0,0,299.29,43.5v-.08c0-7.8-5.77-14.55-14.38-14.55A14.33,14.33,0,0,0,270.45,43.5v.08c0,7.8,5.77,14.55,14.38,14.55Zm.08-3c-6.46,0-11.15-5.26-11.15-11.68v-.08c0-6.42,4.61-11.6,11.07-11.6S296,37.08,296,43.5v.08c0,6.42-4.6,11.6-11.07,11.6Zm22.19,2.46h3.19v-10h6.87c6,0,11.11-3.15,11.11-9.25v-.08c0-5.58-4.21-9-10.59-9H307.1V57.64Zm3.19-12.89V32.31h7.15c4.56,0,7.59,2.1,7.59,6.14v.08c0,3.72-3.07,6.22-7.75,6.22Z'

const CoreShopInlineLogo: React.FC<InlineLogoProps> = ({ className }) => {
  const svgRef = React.useRef<SVGSVGElement>(null)
  const gradientRef = React.useRef<SVGLinearGradientElement>(null)
  const isHoveringRef = React.useRef(false)

  // Auto-shimmer loop when not hovering
  React.useEffect(() => {
    let rafId: number
    const startTime = performance.now()
    const DELAY_MS = 2000
    const CYCLE_MS = 3000

    const tick = (): void => {
      if (!isHoveringRef.current && gradientRef.current != null) {
        const elapsed = performance.now() - startTime
        if (elapsed > DELAY_MS) {
          const t = ((elapsed - DELAY_MS) % CYCLE_MS) / CYCLE_MS
          const offset = t * 2 - 1 // -1 → +1
          gradientRef.current.setAttribute('gradientTransform', `translate(${offset} 0)`)
        }
      }
      rafId = requestAnimationFrame(tick)
    }

    rafId = requestAnimationFrame(tick)
    return () => { cancelAnimationFrame(rafId) }
  }, [])

  const handleMouseMove = React.useCallback((e: React.MouseEvent<SVGSVGElement>) => {
    const svg = svgRef.current
    const gradient = gradientRef.current
    if (svg == null || gradient == null || !isHoveringRef.current) return
    const rect = svg.getBoundingClientRect()
    const svgX = (e.clientX - rect.left) / rect.width
    // Text spans roughly x=85..330 in viewBox (333.77 wide)
    const textRelX = Math.max(0, Math.min(1, (svgX - 0.255) / 0.734))
    gradient.setAttribute('gradientTransform', `translate(${(textRelX - 0.5) * 1.4} 0)`)
  }, [])

  const handleMouseEnter = React.useCallback(() => {
    isHoveringRef.current = true
  }, [])

  const handleMouseLeave = React.useCallback(() => {
    isHoveringRef.current = false
  }, [])

  return (
    <svg
      ref={svgRef}
      aria-label="CoreShop"
      className={className}
      height="87"
      onMouseEnter={handleMouseEnter}
      onMouseLeave={handleMouseLeave}
      onMouseMove={handleMouseMove}
      preserveAspectRatio="xMidYMid meet"
      role="img"
      viewBox="0 0 333.77 87"
      width="333.77"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <linearGradient id="iconGlow" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stopColor="#ff2d2d">
            <animate attributeName="stop-color" dur="3s" repeatCount="indefinite" values="#ff2d2d;#ff6b6b;#cd1017;#ff2d2d" />
          </stop>
          <stop offset="100%" stopColor="#cd1017">
            <animate attributeName="stop-color" dur="3s" repeatCount="indefinite" values="#cd1017;#ff2d2d;#ff6b6b;#cd1017" />
          </stop>
        </linearGradient>
        <filter id="iconBloom">
          <feGaussianBlur in="SourceGraphic" stdDeviation="0">
            <animate attributeName="stdDeviation" dur="3s" repeatCount="indefinite" values="0;2;0" />
          </feGaussianBlur>
        </filter>
        <linearGradient ref={gradientRef} id="textShimmer" gradientUnits="objectBoundingBox" x1="0" x2="1" y1="0" y2="0">
          <stop offset="0%" stopColor="#333333" />
          <stop offset="35%" stopColor="#333333" />
          <stop offset="50%" stopColor="#cd1017" />
          <stop offset="65%" stopColor="#333333" />
          <stop offset="100%" stopColor="#333333" />
        </linearGradient>
      </defs>
      <g>
        <path
          className="logo-icon-glow"
          d={ICON_PATH}
          fill="url(#iconGlow)"
          fillRule="evenodd"
          filter="url(#iconBloom)"
          opacity="0.5"
        />
        <path
          className="logo-icon"
          d={ICON_PATH}
          fill="url(#iconGlow)"
          fillRule="evenodd"
          stroke="#cd1017"
          strokeDasharray="600"
          strokeDashoffset="600"
          strokeWidth="1"
        />
        <path
          className="logo-text"
          d={TEXT_PATH}
          fill="url(#textShimmer)"
          fillRule="evenodd"
        />
      </g>
    </svg>
  )
}

const useStyles = createStyles(({ css, token }) => ({
  modal: css`
    .ant-modal {
      max-width: min(92vw, 560px);
    }

    .ant-modal-content {
      position: relative;
      overflow: hidden;
      border-radius: 24px;
      border: 1px solid ${token.colorBorderSecondary};
      background:
        radial-gradient(110% 90% at 0% 0%, rgba(220, 38, 38, 0.16) 0%, rgba(220, 38, 38, 0) 62%),
        radial-gradient(110% 90% at 100% 100%, rgba(17, 24, 39, 0.1) 0%, rgba(17, 24, 39, 0) 58%),
        linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
      box-shadow: 0 28px 70px rgba(15, 23, 42, 0.24);

      &::before {
        content: '';
        position: absolute;
        inset: -90px -140px auto auto;
        width: 240px;
        height: 240px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.24) 0%, rgba(220, 38, 38, 0) 70%);
        animation: aboutOrb 8s ease-in-out infinite;
        pointer-events: none;
      }

      &::after {
        content: '';
        position: absolute;
        inset: auto auto -110px -110px;
        width: 220px;
        height: 220px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(30, 41, 59, 0.14) 0%, rgba(30, 41, 59, 0) 68%);
        animation: aboutOrb 10s ease-in-out infinite reverse;
        pointer-events: none;
      }
    }

    .ant-modal-header {
      position: relative;
      z-index: 1;
      margin-bottom: 0;
      padding: 20px 24px 6px;
      background: transparent;
      border-bottom: 0;
    }

    .ant-modal-title {
      font-size: 1.75rem;
      font-weight: 700;
      letter-spacing: -0.03em;
    }

    .ant-modal-close {
      top: 16px;
      right: 16px;
      width: 36px;
      height: 36px;
      border-radius: 999px;
      color: ${token.colorTextTertiary};
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(8px);
      transition: all 160ms ease;

      &:hover {
        color: ${token.colorText};
        background: rgba(255, 255, 255, 0.95);
        transform: rotate(90deg);
      }
    }

    .ant-modal-body {
      position: relative;
      z-index: 1;
      padding: 8px 24px 28px;
    }

    @keyframes aboutReveal {
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @keyframes aboutSweep {
      0%,
      100% {
        transform: translateX(-100%) rotate(8deg);
        opacity: 0;
      }
      34% {
        opacity: 1;
      }
      60% {
        transform: translateX(82%) rotate(8deg);
        opacity: 0;
      }
    }

    @keyframes aboutFloat {
      0%,
      100% {
        transform: translateY(0px);
      }
      50% {
        transform: translateY(-4px);
      }
    }

    @keyframes aboutOrb {
      0%,
      100% {
        transform: translate(0, 0) scale(1);
      }
      50% {
        transform: translate(8px, -10px) scale(1.08);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      * {
        animation: none !important;
        transition: none !important;
        transform: none !important;
      }
    }
  `,
  content: css`
    position: relative;
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 14px;
  `,
  revealTwo: css`
    opacity: 0;
    transform: translateY(14px) scale(0.98);
    animation: aboutReveal 460ms cubic-bezier(0.19, 1, 0.22, 1) 120ms forwards;
  `,
  revealThree: css`
    opacity: 0;
    transform: translateY(14px) scale(0.98);
    animation: aboutReveal 460ms cubic-bezier(0.19, 1, 0.22, 1) 180ms forwards;
  `,
  revealFour: css`
    opacity: 0;
    transform: translateY(14px) scale(0.98);
    animation: aboutReveal 460ms cubic-bezier(0.19, 1, 0.22, 1) 240ms forwards;
  `,
  logoShell: css`
    position: relative;
    display: flex;
    justify-content: center;
    opacity: 1;
    width: min(100%, 420px);
    margin-top: 4px;
    padding: 18px 20px;
    border-radius: 20px;
    border: 1px solid rgba(148, 163, 184, 0.26);
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.92) 0%, rgba(248, 250, 252, 0.7) 100%);
    box-shadow:
      inset 0 1px 0 rgba(255, 255, 255, 0.75),
      0 14px 36px rgba(15, 23, 42, 0.12);
    animation: aboutFloat 6s ease-in-out infinite;
    isolation: isolate;

    &::after {
      content: '';
      position: absolute;
      inset: -30% -60%;
      background: linear-gradient(105deg, rgba(255, 255, 255, 0) 36%, rgba(255, 255, 255, 0.75) 50%, rgba(255, 255, 255, 0) 64%);
      transform: translateX(-100%) rotate(8deg);
      animation: aboutSweep 4.8s ease-in-out infinite;
      pointer-events: none;
      z-index: 1;
    }
  `,
  logo: css`
    position: relative;
    z-index: 2;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 110px;
    width: 100%;

  `,
  logoSvg: css`
    display: block;
    width: min(100%, 360px);
    height: auto !important;
    max-height: 92px;
    flex: 0 0 auto;
    filter: drop-shadow(0 10px 28px rgba(15, 23, 42, 0.2));
    overflow: visible;

    .logo-icon {
      fill-opacity: 0;
      animation:
        logoIconDraw 1.4s cubic-bezier(0.65, 0, 0.35, 1) 0.2s forwards,
        logoIconFill 0.6s ease 1.2s forwards;
      transform-origin: 37px 43px;
    }

    .logo-icon-glow {
      opacity: 0;
      animation: logoGlowIn 0.8s ease 1.6s forwards;
      transform-origin: 37px 43px;
    }

    .logo-text {
      clip-path: inset(0 100% 0 0);
      animation: logoTextWipe 0.8s cubic-bezier(0.25, 0.1, 0.25, 1) 0.9s forwards;
    }

    @keyframes logoIconDraw {
      to {
        stroke-dashoffset: 0;
      }
    }

    @keyframes logoIconFill {
      0% {
        fill-opacity: 0;
      }
      100% {
        fill-opacity: 1;
      }
    }

    @keyframes logoGlowIn {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 0.5;
      }
    }

    @keyframes logoTextWipe {
      to {
        clip-path: inset(0 0 0 0);
      }
    }
  `,
  versionPill: css`
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 999px;
    border: 1px solid rgba(15, 23, 42, 0.08);
    background: linear-gradient(135deg, #111827 0%, #374151 100%);
    color: #f8fafc;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.24);
  `,
  copyrightRow: css`
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: ${token.colorTextSecondary};
  `,
  linkBtn: css`
    padding: 0 !important;
    height: auto !important;
    font-weight: 600;
  `,
  actionRow: css`
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  `,
  actionBtn: css`
    height: auto !important;
    padding: 7px 14px !important;
    border-radius: 999px !important;
    border: 1px solid rgba(148, 163, 184, 0.4) !important;
    background: rgba(255, 255, 255, 0.65) !important;
    color: ${token.colorText} !important;
    font-weight: 600 !important;
    text-decoration: none !important;
    transition: all 160ms ease !important;

    &:hover {
      border-color: rgba(220, 38, 38, 0.45) !important;
      color: #991b1b !important;
      background: rgba(255, 255, 255, 0.95) !important;
      transform: translateY(-1px);
    }
  `,
  loading: css`
    display: flex;
    justify-content: center;
    padding: 44px 24px;
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
  const licenseLabel = t('coreshop_license')
  const contactLabel = t('coreshop_contact')

  React.useEffect(() => {
    if (props.open && !version) {
      const fetchVersion = async (): Promise<void> => {
        try {
          const response = await fetch('/pimcore-studio/api/coreshop/settings/get-settings')
          const data: SettingsResponse = await response.json()
          setVersion(data.bundle.version)
        } catch (error) {
          void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to fetch CoreShop version')))
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
      width={540}
    >
      {loading ? (
        <div className={styles.loading}>
          <Spin size="large" />
        </div>
      ) : (
        <div className={styles.content}>
          <div className={styles.logoShell}>
            <div className={styles.logo}>
              <CoreShopInlineLogo className={styles.logoSvg} />
            </div>
          </div>

          <span className={`${styles.versionPill} ${styles.revealTwo}`}>
            Version {version}
          </span>

          <Flex className={`${styles.copyrightRow} ${styles.revealThree}`}>
            <span>
              © by CoreShop GmbH, Wels, Austria
            </span>
            <span>(</span>
            <Button
              className={styles.linkBtn}
              href="https://www.coreshop.com"
              target="_blank"
              type="link"
            >
              coreshop.com
            </Button>
            <span>)</span>
          </Flex>

          <Flex className={`${styles.actionRow} ${styles.revealFour}`}>
            <Button
              className={styles.actionBtn}
              href="https://github.com/coreshop/CoreShop/blob/2026.x/LICENSE.md"
              target="_blank"
              type="link"
            >
              {licenseLabel === 'coreshop_license' ? 'License' : licenseLabel}
            </Button>

            <Button
              className={styles.actionBtn}
              href="https://www.coreshop.com/contact"
              target="_blank"
              type="link"
            >
              {contactLabel === 'coreshop_contact' ? 'Contact' : contactLabel}
            </Button>
          </Flex>
        </div>
      )}
    </Modal>
  )
}
