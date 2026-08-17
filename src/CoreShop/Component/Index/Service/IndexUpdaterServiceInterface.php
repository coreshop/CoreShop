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

namespace CoreShop\Component\Index\Service;

use CoreShop\Component\Index\Model\IndexableInterface;

interface IndexUpdaterServiceInterface
{
    public function updateIndices(IndexableInterface $subject, bool $isVersionChange = false): void;

    public function removeIndices(IndexableInterface $subject): void;

    public function removeFromIndicesById(string $className, int $id): void;
}
