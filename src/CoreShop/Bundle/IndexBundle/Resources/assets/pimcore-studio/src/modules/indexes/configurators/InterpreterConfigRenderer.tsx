/**
 * CoreShop IndexBundle - Interpreter Config Renderer
 *
 * Renders interpreter configuration using SchemaForm.
 * All interpreter types (including nested/iterator) are handled
 * by the schema-driven form system via block prefixes.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import type { IndexConfig } from '../api'

interface InterpreterConfigRendererProps {
  type: string
  value: Record<string, any>
  onChange: (config: Record<string, any>) => void
  indexConfig?: IndexConfig
}

export const InterpreterConfigRenderer: React.FC<InterpreterConfigRendererProps> = ({
  type,
  value,
  onChange,
  indexConfig,
}) => {
  const blockPrefix = indexConfig?.interpreters?.find(i => i.type === type)?.blockPrefix

  if (!blockPrefix) {
    return null
  }

  return <SchemaForm blockPrefix={blockPrefix} embedded data={value || {}} onChange={onChange} />
}
