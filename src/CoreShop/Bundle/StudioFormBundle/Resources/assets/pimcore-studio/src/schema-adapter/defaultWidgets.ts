/**
 * CoreShop Schema Adapter - Default Widget Registrations
 *
 * Registers Ant Design components for standard Symfony form type block prefixes.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { Input, InputNumber, Switch, Select, DatePicker, TimePicker, ColorPicker, Slider, Checkbox, Radio } from 'antd'
import type { WidgetRegistry } from './WidgetRegistry'
import { CollectionWidget } from './CollectionWidget'
import { GridCollectionWidget } from './GridCollectionWidget'

/**
 * Register default Ant Design widget resolvers by Symfony block prefix.
 */
export const registerDefaultWidgets = (registry: WidgetRegistry): void => {
  // TextType → block prefix 'text'
  registry.register('text', () => ({
    component: Input,
  }))

  // TextareaType → block prefix 'textarea'
  registry.register('textarea', () => ({
    component: Input.TextArea,
    props: { rows: 4 },
  }))

  // IntegerType → block prefix 'integer'
  registry.register('integer', () => ({
    component: InputNumber,
    props: { style: { width: '100%' }, precision: 0 },
  }))

  // NumberType → block prefix 'number'
  registry.register('number', () => ({
    component: InputNumber,
    props: { style: { width: '100%' } },
  }))

  // CheckboxType → block prefix 'checkbox'
  registry.register('checkbox', () => ({
    component: Switch,
    valuePropName: 'checked',
  }))

  // ChoiceType → block prefix 'choice'
  registry.register('choice', (field) => {
    const choices = field.choices ?? []
    const flatOptions = choices.map(c => ({
      value: c.value,
      label: c.label,
      group: c.group,
    }))

    if (field.expanded) {
      if (field.multiple) {
        return {
          component: Checkbox.Group,
          props: {
            options: flatOptions.map(({ value, label }) => ({ value, label })),
          },
        }
      }

      return {
        component: Radio.Group,
        props: {
          options: flatOptions.map(({ value, label }) => ({ value, label })),
          optionType: 'default',
        },
      }
    }

    const groupedOptions = new Map<string, Array<{ value: string | number; label: string }>>()
    const options: Array<{ value: string | number; label: string } | { label: string; options: Array<{ value: string | number; label: string }> }> = []
    for (const option of flatOptions) {
      if (!option.group) {
        options.push({ value: option.value, label: option.label })
        continue
      }

      const group = groupedOptions.get(option.group) ?? []
      group.push({ value: option.value, label: option.label })
      groupedOptions.set(option.group, group)
    }
    for (const [groupLabel, groupOptions] of groupedOptions.entries()) {
      options.push({ label: groupLabel, options: groupOptions })
    }

    return {
      component: Select,
      props: {
        options,
        allowClear: true,
        showSearch: true,
        filterOption: (input: string, option: any) =>
          (option?.label ?? '').toLowerCase().includes(input.toLowerCase()),
        mode: field.multiple ? 'multiple' : undefined,
      },
    }
  })

  // TagCollectionType → block prefix 'tag_collection'
  registry.register('tag_collection', () => ({
    component: Select,
    props: {
      mode: 'tags',
      style: { width: '100%' },
      tokenSeparators: [','],
      open: false,
    },
  }))

  // Grid collection → block prefix 'grid_collection'
  registry.register('grid_collection', (field) => ({
    component: GridCollectionWidget,
    props: {
      field,
      widgetRegistry: registry,
    },
  }))

  // CollectionType → block prefix 'collection'
  registry.register('collection', (field) => ({
    component: CollectionWidget,
    props: {
      field,
      widgetRegistry: registry,
    },
  }))

  // HiddenType → block prefix 'hidden'
  registry.register('hidden', () => ({
    component: Input,
    extra: {
      hidden: true,
    },
  }))

  // EmailType → block prefix 'email'
  registry.register('email', () => ({
    component: Input,
    props: { type: 'email' },
  }))

  // UrlType → block prefix 'url'
  registry.register('url', () => ({
    component: Input,
    props: { type: 'url' },
  }))

  // PasswordType → block prefix 'password'
  registry.register('password', () => ({
    component: Input.Password,
  }))

  // DateType → block prefix 'date'
  registry.register('date', () => ({
    component: DatePicker,
    props: { style: { width: '100%' } },
  }))

  // DateTimeType → block prefix 'datetime'
  registry.register('datetime', () => ({
    component: DatePicker,
    props: { showTime: true, style: { width: '100%' } },
  }))

  // TimeType → block prefix 'time'
  registry.register('time', () => ({
    component: TimePicker,
    props: { style: { width: '100%' } },
  }))

  // ColorType → block prefix 'color'
  registry.register('color', () => ({
    component: ColorPicker,
  }))

  // RangeType → block prefix 'range'
  registry.register('range', () => ({
    component: Slider,
  }))
}
