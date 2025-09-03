/**
 * CoreShop IndexBundle Icon Library
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
import { serviceIds } from '@pimcore/studio-ui-bundle/app'
import { type IconLibrary } from '@pimcore/studio-ui-bundle'

import filterBooleanIcon from '@CoreShopIndex/assets/filter-boolean.svg?react'
import filterCategorySelectIcon from '@CoreShopIndex/assets/filter-category-select.svg?react'
import filterCombinedIcon from '@CoreShopIndex/assets/filter-combined.svg?react'
import filterEmptyIcon from '@CoreShopIndex/assets/filter-empty.svg?react'
import filterMultiselectIcon from '@CoreShopIndex/assets/filter-multiselect.svg?react'
import filterRangeIcon from '@CoreShopIndex/assets/filter-range.svg?react'
import filterSearchIcon from '@CoreShopIndex/assets/filter-search.svg?react'
import filterSelectIcon from '@CoreShopIndex/assets/filter-select.svg?react'
import filtersIcon from '@CoreShopIndex/assets/filters.svg?react'
import indexesIcon from '@CoreShopIndex/assets/indexes.svg?react'
import searchIcon from '@CoreShopIndex/assets/search.svg?react'
import similaritiesFieldIcon from '@CoreShopIndex/assets/similarities-field.svg?react'
import similarityIcon from '@CoreShopIndex/assets/similarity.svg?react'

export const IndexBundleIconExtension: AbstractModule = {
  name: 'coreshop-index-icon-extension',

  onInit(): void {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

    // Index icons
    iconLibrary.register({
      name: 'coreshop_index',
      component: indexesIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_indexes',
      component: indexesIcon
    })

    iconLibrary.register({
      name: 'coreshop_indexes',
      component: indexesIcon
    })

    // Filter icons
    iconLibrary.register({
      name: 'coreshop_filter',
      component: filtersIcon
    })

    iconLibrary.register({
      name: 'coreshop_nav_icon_filters',
      component: filtersIcon
    })

    iconLibrary.register({
      name: 'coreshop_filters',
      component: filtersIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_boolean',
      component: filterBooleanIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_category_select',
      component: filterCategorySelectIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_combined',
      component: filterCombinedIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_empty',
      component: filterEmptyIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_multiselect',
      component: filterMultiselectIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_range',
      component: filterRangeIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_search',
      component: filterSearchIcon
    })

    iconLibrary.register({
      name: 'coreshop_filter_select',
      component: filterSelectIcon
    })

    // Search icons
    iconLibrary.register({
      name: 'coreshop_search',
      component: searchIcon
    })

    // Similarity icons
    iconLibrary.register({
      name: 'coreshop_similarity',
      component: similarityIcon
    })

    iconLibrary.register({
      name: 'coreshop_similarities_field',
      component: similaritiesFieldIcon
    })
  }
}
