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

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;

final class IndexConditionContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Transform /^condition$/
     */
    public function condition()
    {
        return $this->sharedStorage->get('index_condition');
    }

    /**
     * @Transform /^condition "([^"]+)"$/
     */
    public function conditionWithIdentifier($identifier)
    {
        return $this->sharedStorage->get('index_condition_' . $identifier);
    }

    /**
     * @Transform /^conditions "([^"]+)"$/
     */
    public function conditionsWithIdentifiers($identifiers): array
    {
        $conditions = [];

        foreach (explode(',', $identifiers) as $identifier) {
            $identifier = trim($identifier);

            if ($this->sharedStorage->has('index_condition_' . $identifier)) {
                $conditions[] = $this->sharedStorage->get('index_condition_' . $identifier);
            }
        }

        return $conditions;
    }
}
