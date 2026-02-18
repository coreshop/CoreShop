/**
 * CoreShop IndexBundle - Interpreter Schema Context
 *
 * Provides interpreter type → blockPrefix mapping to nested components.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'

type InterpreterSchemaMap = Record<string, string>

const InterpreterSchemaContext = React.createContext<InterpreterSchemaMap>({})

export const InterpreterSchemaProvider = InterpreterSchemaContext.Provider

export const useInterpreterSchema = (): InterpreterSchemaMap => {
  return React.useContext(InterpreterSchemaContext)
}
