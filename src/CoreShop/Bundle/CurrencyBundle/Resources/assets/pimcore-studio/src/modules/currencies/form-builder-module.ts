/**
 * CoreShop CurrencyBundle - Currency Form Builder Module
 *
 * Registers the Currency form builder in the container.
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { createCurrencyFormBuilder } from './CurrencyFormBuilder'

export const CurrencyFormBuilderModule: AbstractModule = {
  onInit(): void {
    const builder = createCurrencyFormBuilder()

    // Register in container so other bundles can access and extend it
    container.bind('CoreShop/Currency/Currency/FormBuilder')
      .toConstantValue(builder)
  }
}
