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

namespace CoreShop\Component\Rule\Repository;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use CoreShop\Component\Rule\Model\RuleInterface;

interface RuleRepositoryInterface extends RepositoryInterface
{
    /**
     * @return RuleInterface[]
     */
    public function findActive(): array;

    /**
     * @param string $conditionType
     *
     * @return RuleInterface[]
     */
    public function findWithConditionOfType($conditionType): array;

    /**
     * @param string $actionType
     *
     * @return RuleInterface[]
     */
    public function findWithActionOfType($actionType): array;
}
