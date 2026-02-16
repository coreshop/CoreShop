/**
 * CoreShop IndexBundle - Interpreter Config Renderer
 *
 * Renders interpreter configuration using SchemaForm for simple types
 * and custom components for recursive types (nested/iterator).
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import { NESTED_INTERPRETER_TYPES, ITERATOR_INTERPRETER_TYPE } from './index'
import type { IndexConfig } from '../api'

interface InterpreterConfigRendererProps {
  type: string
  value: Record<string, any>
  onChange: (config: Record<string, any>) => void
  indexConfig?: IndexConfig
  depth?: number
}

export const InterpreterConfigRenderer: React.FC<InterpreterConfigRendererProps> = ({
  type,
  value,
  onChange,
  indexConfig,
  depth = 0
}) => {
  const blockPrefix = indexConfig?.interpreters?.find(i => i.type === type)?.blockPrefix

  if (blockPrefix) {
    return <SchemaForm blockPrefix={blockPrefix} data={value || {}} onChange={onChange} />
  }

  if (NESTED_INTERPRETER_TYPES.includes(type)) {
    // Lazy import to avoid circular dependency
    const { NestedInterpreterConfigurator } = require('./NestedInterpreterConfigurator')
    return (
      <NestedInterpreterConfigurator
        value={value}
        onChange={onChange}
        indexConfig={indexConfig}
        depth={depth}
      />
    )
  }

  if (type === ITERATOR_INTERPRETER_TYPE) {
    const { IteratorInterpreterConfigurator } = require('./IteratorInterpreterConfigurator')
    return (
      <IteratorInterpreterConfigurator
        value={value}
        onChange={onChange}
        indexConfig={indexConfig}
        depth={depth}
      />
    )
  }

  return null
}
