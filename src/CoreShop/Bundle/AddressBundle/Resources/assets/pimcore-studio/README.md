# CoreShop Address Bundle - Pimcore Studio Plugin

This is the Pimcore Studio plugin for the CoreShop Address Bundle, providing address management functionality (Countries, States, Zones) in the new React-based Pimcore Studio UI.

## Overview

This plugin extends the CoreShop Resource Bundle to provide address-specific resource management. It replaces the classic ExtJS-based admin interface with modern React components for managing geographical data.

## Migration from Classic Admin

This plugin migrates the following ExtJS components:

### Original ExtJS Files (Resources/public/pimcore/js/)
- `resource.js` → `AddressPlugin.ts`
- `country/panel.js` → `components/CountryPanel.tsx`
- `country/item.js` → `components/CountryItem.tsx`
- `state/panel.js` → `components/StatePanel.tsx`
- `zone/panel.js` → `components/ZonePanel.tsx`
- Core extension data types → React form components

### New React Architecture

#### Components
- **CountryPanel**: Manage countries with zones and ISO codes
- **StatePanel**: Manage states/provinces linked to countries
- **ZonePanel**: Manage geographical zones containing countries
- **CountryItem**: Country editing form with zone assignment

#### Data Management Hooks
- **useCountries**: Reactive countries data with zone filtering
- **useStates**: Reactive states data with country filtering  
- **useZones**: Reactive zones data management

## Installation

### Prerequisites
- CoreShop Resource Bundle Studio Plugin
- Node.js 18+
- Pimcore Studio UI Bundle

### Setup

1. **Install dependencies:**
   ```bash
   cd src/CoreShop/Bundle/AddressBundle/Resources/assets/pimcore-studio
   npm install
   ```

2. **Build the plugin:**
   ```bash
   npm run build
   ```

## Usage

### Country Management

```typescript
import { CountryPanel, CountryItem } from '@coreshop/address-studio-plugin'

// List all countries
<CountryPanel onItemSelect={(country) => openCountryEditor(country)} />

// Edit a specific country
<CountryItem 
  data={countryData}
  onSave={(country) => console.log('Country saved:', country)}
  onCancel={() => closeEditor()}
/>
```

### Using Data Hooks

```typescript
import { useCountries, useStates, useZones } from '@coreshop/address-studio-plugin'

// Load all countries
const { countries, loading } = useCountries()

// Load countries for a specific zone
const { countries: zoneCountries } = useCountries({ zoneId: 1 })

// Load states for a specific country  
const { states } = useStates({ countryId: 1 })

// Load all zones
const { zones } = useZones()
```

## Features

### Countries
- ✅ Country name and ISO code management
- ✅ Zone assignment for geographical grouping
- ✅ Active/inactive status
- ✅ Integration with Pimcore data object fields
- ✅ Validation for ISO codes (2-letter uppercase)

### States/Provinces  
- ✅ State name and ISO code management
- ✅ Country assignment
- ✅ Active/inactive status
- ✅ Filtering by country

### Zones
- ✅ Zone name management
- ✅ Country assignment to zones
- ✅ Active/inactive status
- ✅ Countries count display

### Pimcore Integration

The plugin provides React-based replacements for CoreShop's Pimcore data object fields:

#### Original ExtJS Data Types → React Components
- `coreShopCountry` → Country selector component
- `coreShopCountryMultiselect` → Multi-country selector
- `coreShopState` → State selector component  
- `coreShopAddressIdentifier` → Address identifier selector
- `coreShopZone` → Zone selector component

## Type Definitions

### Core Address Types

```typescript
interface Country extends CoreShopResource {
  name: string
  isoCode: string
  active: boolean
  zone?: Zone
  zoneName?: string
  states?: State[]
}

interface State extends CoreShopResource {
  name: string
  isoCode: string
  active: boolean
  country?: Country
  countryId?: number
}

interface Zone extends CoreShopResource {
  name: string
  active: boolean
  countries?: Country[]
}
```

## API Routes

The plugin works with existing CoreShop API endpoints:

### Countries
- `GET /admin/api/coreshop/country/list` - List countries
- `POST /admin/api/coreshop/country/add` - Create country
- `GET /admin/api/coreshop/country/get/{id}` - Get country
- `PUT /admin/api/coreshop/country/{id}` - Update country
- `DELETE /admin/api/coreshop/country/delete/{id}` - Delete country

### States  
- `GET /admin/api/coreshop/state/list` - List states
- `GET /admin/api/coreshop/country/{id}/states` - States by country

### Zones
- `GET /admin/api/coreshop/zone/list` - List zones
- `GET /admin/api/coreshop/zone/{id}/countries` - Countries by zone

## Development

### Project Structure
```
src/
├── components/          # React components
│   ├── CountryPanel.tsx
│   ├── CountryItem.tsx
│   ├── StatePanel.tsx
│   └── ZonePanel.tsx
├── hooks/              # Custom React hooks
│   ├── useCountries.ts
│   ├── useStates.ts
│   └── useZones.ts
├── types/              # TypeScript definitions
│   └── index.ts
├── AddressPlugin.ts    # Main plugin class
└── main.ts            # Plugin entry point
```

### Extending Address Components

```typescript
// Custom country form fields
const CustomCountryForm = ({ country }) => (
  <ResourceItem config={countryConfig}>
    <Form.Item name="isoCode" label="ISO Code" rules={[/* validation */]}>
      <Input maxLength={2} style={{ textTransform: 'uppercase' }} />
    </Form.Item>
    
    <Form.Item name="zone" label="Zone">
      <Select>
        {zones.map(zone => (
          <Select.Option key={zone.id} value={zone.id}>
            {zone.name}
          </Select.Option>
        ))}
      </Select>
    </Form.Item>
    
    {/* Custom fields */}
    <Form.Item name="currency" label="Default Currency">
      <CurrencySelector />
    </Form.Item>
  </ResourceItem>
)
```

## Validation

### Country Validation
- Name: Required, min 2 characters
- ISO Code: Required, exactly 2 uppercase letters (e.g., "US", "DE")
- Zone: Optional zone assignment

### State Validation  
- Name: Required, min 2 characters
- ISO Code: Optional, 2-3 characters
- Country: Required country assignment

### Zone Validation
- Name: Required, min 2 characters
- Countries: Optional country assignments

## Migration Notes

### From ExtJS Address Resources

1. **Grouped Country Display**: ExtJS grid grouping by zone → Ant Design Table with zone tags
2. **Form Validation**: ExtJS validators → Ant Design Form rules  
3. **Data Loading**: ExtJS stores → React hooks with loading states
4. **Event Handling**: ExtJS events → React callbacks and context

### Backward Compatibility

- All existing API routes continue to work
- Database schema remains unchanged
- Pimcore data object field types maintain compatibility
- Classic admin can coexist during migration period

## Contributing

When extending this plugin:

1. Follow established patterns from the Resource Bundle
2. Maintain type safety with proper TypeScript definitions
3. Add proper form validation for data integrity
4. Consider geographical data standards (ISO codes, etc.)
5. Test with existing CoreShop installations

## Dependencies

- `@coreshop/resource-studio-plugin`: Base resource functionality
- `@pimcore/studio-ui-bundle`: Pimcore Studio UI framework
- `antd`: UI component library
- `react`: UI framework

## License

CoreShop Commercial License (CCL) - Same as CoreShop core