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

namespace CoreShop\Component\Resource\Repository;

use CoreShop\Component\Resource\Model\ResourceInterface;
use Doctrine\Persistence\ObjectRepository;

interface RepositoryInterface extends ObjectRepository
{
    public const string ORDER_ASCENDING = 'ASC';

    public const string ORDER_DESCENDING = 'DESC';

    public function add(ResourceInterface $resource): void;

    public function remove(ResourceInterface $resource): void;
}
