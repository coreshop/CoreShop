/**
 * CoreShop PimcoreBundle Authentication Utilities
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import { store } from '@pimcore/studio-ui-bundle/app'

const isAuthenticated = (): boolean =>
  (store.getState() as Record<string, any>)?.authentication?.isAuthenticated === true

/**
 * Resolves once the Pimcore Studio Redux store reports the user as authenticated.
 *
 * Module `onInit()` runs during app bootstrap — before login — so any API calls made
 * there fail with 401. Gate post-auth work (menu/nav registration, stack config lookups,
 * etc.) behind this helper so it runs after the login transition.
 */
export const waitForAuthentication = async (): Promise<void> => {
  if (isAuthenticated()) {
    return
  }

  return new Promise<void>((resolve) => {
    const unsubscribe = store.subscribe(() => {
      if (isAuthenticated()) {
        unsubscribe()
        resolve()
      }
    })
  })
}
