import { EntityApi } from '@coreshop/resource/src/entities/api'

export interface GatewayConfig {
  factoryName: string
  gatewayName: string
  config: any[]
  decryptedConfig: any
  id: number
}

export interface PaymentProviderTranslation {
  title?: string
  description?: string
  instructions?: string
}

export interface PaymentProviderRuleGroup {
  id?: number
  priority: number
  stopPropagation: boolean
  paymentProviderRule?: any // TODO: Define PaymentProviderRule interface
}

export interface PaymentProvider extends Record<string, any> {
  id?: number
  identifier?: string
  active?: boolean
  position?: number
  logo?: any // Asset
  translations?: Record<string, PaymentProviderTranslation>
  gatewayConfig?: GatewayConfig
  paymentProviderRules?: PaymentProviderRuleGroup[]
}

class PaymentProviderApi extends EntityApi<PaymentProvider> { }

export const paymentProviderApi = new PaymentProviderApi({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/payment_providers'
})
