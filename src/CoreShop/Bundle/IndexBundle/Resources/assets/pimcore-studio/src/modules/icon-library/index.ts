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
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library'

// @ts-ignore
import filterBooleanIcon from '../../assets/filter-boolean.svg?react'
// @ts-ignore
import filterCategorySelectIcon from '../../assets/filter-category-select.svg?react'
// @ts-ignore
import filterCombinedIcon from '../../assets/filter-combined.svg?react'
// @ts-ignore
import filterEmptyIcon from '../../assets/filter-empty.svg?react'
// @ts-ignore
import filterMultiselectIcon from '../../assets/filter-multiselect.svg?react'
// @ts-ignore
import filterRangeIcon from '../../assets/filter-range.svg?react'
// @ts-ignore
import filterSearchIcon from '../../assets/filter-search.svg?react'
// @ts-ignore
import filterSelectIcon from '../../assets/filter-select.svg?react'
// @ts-ignore
import filtersIcon from '../../assets/filters.svg?react'
// @ts-ignore
import indexesIcon from '../../assets/indexes.svg?react'
// @ts-ignore
import searchIcon from '../../assets/search.svg?react'
// @ts-ignore
import similaritiesFieldIcon from '../../assets/similarities-field.svg?react'
// @ts-ignore
import similarityIcon from '../../assets/similarity.svg?react'

export const IndexBundleIconModule: AbstractModule = {
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
