/**
 * CoreShop Schema Adapter - Default Widget Registrations
 *
 * Registers Ant Design components for standard Symfony form types.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { Input, InputNumber, Switch, Select, DatePicker, TimePicker, ColorPicker, Slider } from 'antd'
import type { WidgetRegistry } from './WidgetRegistry'
import { AutocompleteWidget } from './widgets/AutocompleteWidget'

/**
 * Register default Ant Design widget resolvers.
 */
export const registerDefaultWidgets = (registry: WidgetRegistry): void => {
  // Text input
  registry.register('input', () => ({
    component: Input,
  }))

  // Textarea
  registry.register('textarea', () => ({
    component: Input.TextArea,
    props: { rows: 4 },
  }))

  // Number input
  registry.register('inputNumber', () => ({
    component: InputNumber,
    props: { style: { width: '100%' } },
  }))

  // Switch (boolean)
  registry.register('switch', () => ({
    component: Switch,
    valuePropName: 'checked',
  }))

  // Select (choice type)
  registry.register('select', (field) => {
    const choices = field.uiType.choices ?? []
    const options = choices.map(c => ({
      value: c.value,
      label: c.label,
    }))

    return {
      component: Select,
      props: {
        options,
        allowClear: true,
        showSearch: true,
        filterOption: (input: string, option: any) =>
          (option?.label ?? '').toLowerCase().includes(input.toLowerCase()),
        mode: field.uiType.multiple ? 'multiple' : undefined,
      },
    }
  })

  // Entity select (resolved by bundle-specific registrations)
  registry.register('entitySelect', (field) => {
    // entitySelect is typically overridden by specific bundles
    // Fallback: render a basic Select with a hint
    return {
      component: Select,
      props: {
        placeholder: `Select ${field.uiType.entityType ?? 'entity'}...`,
        allowClear: true,
        showSearch: true,
      },
    }
  })

  // Collection of text entries (tags input)
  registry.register('collection', () => ({
    component: Select,
    props: {
      mode: 'tags',
      tokenSeparators: [','],
      open: false,
      suffixIcon: null,
    },
  }))

  // Hidden (not rendered)
  registry.register('hidden', () => ({
    component: Input,
    extra: {
      hidden: true,
    },
  }))

  // Email input
  registry.register('email', () => ({
    component: Input,
    props: { type: 'email' },
  }))

  // URL input
  registry.register('url', () => ({
    component: Input,
    props: { type: 'url' },
  }))

  // Password input
  registry.register('password', () => ({
    component: Input.Password,
  }))

  // Date picker
  registry.register('datePicker', () => ({
    component: DatePicker,
    props: { style: { width: '100%' } },
  }))

  // Date+time picker
  registry.register('dateTimePicker', () => ({
    component: DatePicker,
    props: { showTime: true, style: { width: '100%' } },
  }))

  // Time picker
  registry.register('timePicker', () => ({
    component: TimePicker,
    props: { style: { width: '100%' } },
  }))

  // Color picker
  registry.register('colorPicker', () => ({
    component: ColorPicker,
  }))

  // Range slider
  registry.register('slider', () => ({
    component: Slider,
  }))

  // Autocomplete (server-side search)
  registry.register('autocomplete', (field) => ({
    component: AutocompleteWidget,
    props: {
      autocompleteClass: field.uiType.autocompleteClass,
      url: field.uiType.url,
      multiple: field.uiType.multiple ?? false,
      minChars: field.uiType.minChars ?? 1,
    },
  }))
}
