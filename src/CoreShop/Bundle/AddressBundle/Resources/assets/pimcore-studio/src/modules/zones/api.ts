import { EntityApi } from '@coreshop/resource/src/entities/api'

export interface ZoneDetail {
  id: number
  name: string
  countries: number[]
  active: boolean
}

export const zoneApi = new EntityApi<ZoneDetail>({
  basePath: '/pimcore-studio/api',
  resourcePath: '/coreshop/zones'
})
