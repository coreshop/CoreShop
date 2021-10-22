<?php

/** @noinspection ALL */
/* @noinspection ALL */
/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Model\Index;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use CoreShop\Component\Resource\Exception\ImplementedByPimcoreException;
use CoreShop\Component\Resource\Pimcore\Model\AbstractPimcoreModel;

class TestEnableIndex extends AbstractPimcoreModel implements IndexableInterface
{
    public function getIndexable(IndexInterface $index): bool
    {
        return $this->getIndexableEnabled($index) && $this->getPublished();
    }

    public function getIndexableEnabled(IndexInterface $index): bool
    {
        $enabled = $this->getEnabled();

        if (is_bool($enabled)) {
            return $enabled;
        }

        return false;
    }

    public function getIndexableName(IndexInterface $index, string $language): string
    {
        $name = $this->getName($language);

        if (null === $name) {
            return '';
        }

        if (!is_string($name)) {
            return '';
        }

        return $name;
    }

    public function getEnabled()
    {
        return new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }

    public function getName($language)
    {
        return new ImplementedByPimcoreException(__CLASS__, __METHOD__);
    }
}
