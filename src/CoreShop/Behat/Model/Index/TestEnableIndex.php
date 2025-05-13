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

namespace CoreShop\Behat\Model\Index;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

/** @noinspection ALL */
class TestEnableIndex extends AbstractPimcoreModel implements IndexableInterface
{
    public function getIndexable(IndexInterface $index): bool
    {
        return $this->getIndexableEnabled($index) && $this->getPublished();
    }

    public function getIndexableEnabled(IndexInterface $index): bool
    {
        return $this->getEnabled() ?? false;
    }

    public function getIndexableName(IndexInterface $index, string $language): string
    {
        return $this->getName($language) ?? '';
    }

    public function getEnabled(): ?bool
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getName(?string $language): ?string
    {
        throw new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
