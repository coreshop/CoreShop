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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\RuleBundle\DependencyInjection\Compiler;

use CoreShop\Component\Registry\RegisterSimpleRegistryTypePass;

class RuleAvailabilityAssessorPass extends RegisterSimpleRegistryTypePass
{
    public const RULE_AVAILABILITY_ASSESSOR_TAG = 'coreshop.registry.rule_availability_assessor';

    public function __construct(
        ) {
        parent::__construct(
            'coreshop.registry.rule_availability_assessor',
            'coreshop.registry.rule_availability_assessors',
            self::RULE_AVAILABILITY_ASSESSOR_TAG,
        );
    }
}
