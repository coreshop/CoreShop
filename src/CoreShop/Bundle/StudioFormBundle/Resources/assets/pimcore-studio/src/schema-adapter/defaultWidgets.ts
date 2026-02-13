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

import { Input, InputNumber, Switch, Select, DatePicker, TimePicker, ColorPicker, Slider } from 'antd'
import type { WidgetRegistry } from './WidgetRegistry'

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
        mode: field.multiple ? 'multiple' : undefined,
      },
    }
  })

  // CollectionType → block prefix 'collection'
  registry.register('collection', () => ({
    component: Select,
    props: {
      mode: 'tags',
      tokenSeparators: [','],
      open: false,
      suffixIcon: null,
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
