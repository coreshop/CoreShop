/**
 * CoreShop Address Plugin
 * 
 * Main plugin class that extends the resource management system with address-specific functionality
 * Replaces the ExtJS address.resource with React-based components
 */

import { eventManager, coreshop } from './services/EventManager'
import { CountryPanel } from './components/CountryPanel'
import { StatePanel } from './components/StatePanel' 
import { ZonePanel } from './components/ZonePanel'

export class AddressPlugin {
  constructor() {
    this.initialize()
  }

  private initialize() {
    // Register this plugin as an address resource handler
    const addressResource = {
      openResource: this.openResource.bind(this)
    }

    // Register with the global resource system
    eventManager.fireEvent('resource.register', {
      name: 'coreshop.address',
      resource: addressResource
    })

    // Initialize stores (equivalent to the original coreshop.global.addStoreWithRoute calls)
    this.initializeStores()

    console.log('[CoreShop Address Plugin] Initialized successfully')
  }

  private initializeStores() {
    // These would typically be handled by a store management system in a full React app
    // For now, we'll set up the basic structure that components can use
    
    const stores = {
      zones: {
        endpoint: 'zone/list',
        fields: ['id', 'name', 'active']
      },
      countries: {
        endpoint: 'country/list', 
        fields: ['id', 'name', 'active', 'isoCode', 'zoneName'],
        sortField: 'name'
      },
      addressIdentifiers: {
        endpoint: 'address-identifier/list',
        fields: ['id', 'name', 'pattern'],
        sortField: 'name'
      },
      states: {
        endpoint: 'state/list',
        fields: ['id', 'name', 'isoCode', 'active', 'countryId']
      }
    }

    // Make stores available globally for components
    if (typeof window !== 'undefined') {
      window.coreshop = window.coreshop || coreshop
      window.coreshop.address = {
        stores
      }
    }
  }

  /**
   * Open a specific address resource
   */
  openResource(item: string): void {
    switch (item) {
      case 'country':
        this.openCountryResource()
        break
      case 'state':
        this.openStateResource()
        break
      case 'zone':
        this.openZoneResource()
        break
      default:
        console.warn(`[CoreShop Address Plugin] Unknown resource: ${item}`)
    }
  }

  private openCountryResource(): void {
    // In a full Pimcore Studio integration, this would open the component in a tab
    // For now, we'll prepare the configuration needed
    const config = {
      layoutId: 'coreshop_countries_panel',
      storeId: 'coreshop_countries',
      iconCls: 'coreshop_icon_country',
      type: 'coreshop_countries',
      title: 'Countries',
      routing: {
        add: 'coreshop_country_add',
        delete: 'coreshop_country_delete',
        get: 'coreshop_country_get',
        list: 'coreshop_country_list'
      }
    }

    console.log('[CoreShop Address Plugin] Opening country resource', config)
    // TODO: Integrate with Pimcore Studio tab system
    // new CountryPanel(config)
  }

  private openStateResource(): void {
    const config = {
      layoutId: 'coreshop_states_panel',
      storeId: 'coreshop_states', 
      iconCls: 'coreshop_icon_state',
      type: 'coreshop_states',
      title: 'States',
      routing: {
        add: 'coreshop_state_add',
        delete: 'coreshop_state_delete',
        get: 'coreshop_state_get',
        list: 'coreshop_state_list'
      }
    }

    console.log('[CoreShop Address Plugin] Opening state resource', config)
    // TODO: Integrate with Pimcore Studio tab system
    // new StatePanel(config)
  }

  private openZoneResource(): void {
    const config = {
      layoutId: 'coreshop_zones_panel',
      storeId: 'coreshop_zones',
      iconCls: 'coreshop_icon_zone', 
      type: 'coreshop_zones',
      title: 'Zones',
      routing: {
        add: 'coreshop_zone_add',
        delete: 'coreshop_zone_delete',
        get: 'coreshop_zone_get',
        list: 'coreshop_zone_list'
      }
    }

    console.log('[CoreShop Address Plugin] Opening zone resource', config)
    // TODO: Integrate with Pimcore Studio tab system
    // new ZonePanel(config)
  }
}