/**
 * CoreShop ResourceBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Select } from 'antd'
import { container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import {
  DynamicTypeDocumentEditableAbstract,
  type AbstractDocumentEditableDefinition,
  type DynamicTypeDocumentEditableRegistry
} from '@pimcore/studio-ui-bundle/modules/element'

type StoreEntry = [string | number | null, string] | string | number

/**
 * The CoreShop editable emits its option list as `config.store` (array of `[id, label]`
 * tuples) via the PHP CoreExtension\Document\Select. We map that straight to antd options.
 */
const toOptions = (store?: StoreEntry[]): Array<{ value: string, label: string }> =>
  (store ?? []).map((entry) => {
    if (Array.isArray(entry)) {
      return { value: String(entry[0]), label: String(entry[1]) }
    }

    return { value: String(entry), label: String(entry) }
  })

const CoreShopDocumentSelect: React.FC<{
  value?: string | number | null
  onChange?: (value: any) => void
  store?: StoreEntry[]
  width?: number
}> = ({ value, onChange, store, width }) => (
  <Select
    allowClear
    // Render the dropdown inside the editable's own container instead of document.body:
    // the Studio document editor renders editables inside an iframe overlay where a
    // body-portalled popup gets clipped / is invisible.
    getPopupContainer={ (node) => (node?.parentElement ?? document.body) }
    onChange={ (next) => onChange?.(next ?? null) }
    optionFilterProp="label"
    options={ toOptions(store) }
    popupMatchSelectWidth={ false }
    showSearch
    style={ { width: width ?? '100%', minWidth: 200 } }
    value={ value !== null && value !== undefined && value !== '' ? String(value) : undefined }
  />
)

/**
 * Studio document editable for CoreShop resource-backed select editables
 * (`coreshop_filter`, `coreshop_index`, `coreshop_country`, ...).
 *
 * It extends the framework's `DynamicTypeDocumentEditableAbstract` (not the concrete
 * `DynamicTypeDocumentEditableSelect`, which is not part of the public SDK surface on all
 * Studio versions) and renders its own antd select from the backend-provided `config.store`.
 * The only thing that differs per feature bundle is the editable `type` id, bound here.
 */
export class DynamicTypeDocumentEditableCoreShopSelect extends DynamicTypeDocumentEditableAbstract {
  id: string

  constructor (type: string) {
    super()
    this.id = type
  }

  getEditableDataComponent (
    props: AbstractDocumentEditableDefinition
  ): React.ReactElement<AbstractDocumentEditableDefinition> {
    return (
      <CoreShopDocumentSelect
        onChange={ props.onChange }
        store={ props.config?.store }
        value={ props.value }
        width={ props.config?.width }
      />
    ) as React.ReactElement<AbstractDocumentEditableDefinition>
  }

  isEmpty (value: any): boolean {
    return value === null || value === undefined || value === ''
  }
}

/**
 * Registers CoreShop select-style document editables in the Studio document editor.
 *
 * Safe to call from any bundle plugin's `onInit`, including inside the document editor
 * iframe (a reduced Studio app), as it only depends on the core DocumentEditableRegistry.
 */
export function registerCoreShopDocumentEditableSelects (types: string[]): void {
  const registry = container.get<DynamicTypeDocumentEditableRegistry>(
    serviceIds['DynamicTypes/DocumentEditableRegistry']
  )

  for (const type of types) {
    registry.registerDynamicType(new DynamicTypeDocumentEditableCoreShopSelect(type))
  }
}
