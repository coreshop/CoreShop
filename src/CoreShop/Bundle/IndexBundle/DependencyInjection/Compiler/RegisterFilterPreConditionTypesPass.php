<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

class RegisterFilterPreConditionTypesPass extends RegisterRegistryTypePass
{
    public const string INDEX_FILTER_PRE_CONDITION_TAG = 'coreshop.filter.pre_condition_type';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.filter.pre_condition_types',
            'coreshop.form_registry.filter.pre_condition_types',
            'coreshop.filter.pre_condition_types',
            self::INDEX_FILTER_PRE_CONDITION_TAG,
        );
    }
}
