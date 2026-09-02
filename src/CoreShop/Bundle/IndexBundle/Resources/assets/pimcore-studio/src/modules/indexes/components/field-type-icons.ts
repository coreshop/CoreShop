/**
 * CoreShop IndexBundle Field Type Icons
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

/**
 * Maps Pimcore and CoreShop field types to icon names
 * Uses only icons that are actually available in Pimcore's icon library
 */
const icons: Record<string, string> = {
  // Basic Pimcore types
  'input': 'text-field',
  'textarea': 'long-text',
  'wysiwyg': 'wysiwyg-field',
  'checkbox': 'checkbox',
  'numeric': 'number-field',
  'number': 'number-field',
  'select': 'chevron-down',
  'multiselect': 'multi-select',
  'date': 'calendar',
  'datetime': 'calendar',
  'time': 'date-time-field',

  // Media
  'image': 'image',
  'hotspotimage': 'image',
  'advancedImage': 'image',
  'video': 'video',
  'asset': 'asset',

  // Relations
  'manyToOneRelation': 'data-object',
  'advancedManyToOneRelation': 'data-object',
  'manyToManyRelation': 'many-to-many',
  'advancedManyToManyRelation': 'many-to-many',
  'manyToManyObjectRelation': 'data-object',
  'reverseObjectRelation': 'data-object',
  'object': 'data-object',
  'objects': 'data-object',

  // Links
  'href': 'many-to-many',
  'multihref': 'many-to-many',
  'urlSlug': 'many-to-many',

  // Structure
  'folder': 'folder',
  'panel': 'layout',
  'layout': 'layout',
  'fieldcollections': 'collection',
  'localizedfields': 'country-select',
  'block': 'collection',
  'table': 'columns',
  'structuredTable': 'columns',

  // Localization
  'country': 'country-select',
  'countries': 'country-select',
  'language': 'country-select',
  'languages': 'country-select',

  // Special fields
  'currency': 'coreshop_icon_currency',
  'quantityValue': 'number-field',
  'inputQuantityValue': 'number-field',
  'calculatedValue': 'calculator',
  'data': 'widget',

  // CoreShop field types
  'coreShopRelation': 'many-to-many',
  'coreShopRelations': 'many-to-many',
  'coreShopMoney': 'coreshop_icon_currency',
  'coreShopMoneyCurrency': 'coreshop_icon_currency',
  'coreShopCurrency': 'coreshop_icon_currency',
  'coreShopCurrencyMultiselect': 'coreshop_icon_currency',
  'coreShopProductSpecificPriceRules': 'coreshop_icon_currency',
  'coreShopProductUnitDefinitions': 'number-field',
  'coreShopProductQuantityPriceRules': 'coreshop_icon_currency',
  'coreShopStoreValues': 'coreshop_store',
  'coreShopStore': 'coreshop_store',
  'coreShopQuantityValue': 'number-field',
  'coreShopQuantityPrice': 'coreshop_icon_currency',
  'coreShopSeo': 'seo',
  'coreShopPaymentProvider': 'coreshop_icon_payment_provider',
  'coreShopPaymentProviderMultiselect': 'coreshop_icon_payment_provider',
  'coreShopCarrier': 'coreshop_carriers',
  'coreShopCarrierMultiselect': 'coreshop_carriers',
  'coreShopShippingRule': 'coreshop_shipping',
  'coreShopTaxRate': 'coreshop_icon_currency',
  'coreShopTaxRuleGroup': 'coreshop_icon_currency',
  'coreShopCountry': 'country-select',
  'coreShopCountryMultiselect': 'country-select',
  'coreShopState': 'country-select',
  'coreShopAddressIdentifier': 'widget',
  'coreShopSuperBoxSelect': 'chevron-down',
  'coreShopItemSelector': 'multi-select',
  'coreShopDynamicDropdown': 'chevron-down',
  'coreShopDynamicDropdownMultiple': 'multi-select',
  'coreShopSerializedData': 'widget'
}

export const getIconForFieldType = (fieldType: string): string => icons[fieldType] ?? 'widget'
