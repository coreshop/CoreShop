<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterRegistryTypePass;

class RegisterFilterUserConditionTypesPass extends RegisterRegistryTypePass
{
    public const INDEX_FILTER_USER_CONDITION_TAG = 'coreshop.filter.user_condition_type';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.filter.user_condition_types',
            'coreshop.form_registry.filter.user_condition_types',
            'coreshop.filter.user_condition_types',
            self::INDEX_FILTER_USER_CONDITION_TAG,
        );
    }
}
