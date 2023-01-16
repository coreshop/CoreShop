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

namespace CoreShop\Component\Index\Model;

interface IndexableInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getClassId();

    /**
     * @return string
     */
    public function getClassName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return mixed
     */
    public function getParent();

    public function getIndexableEnabled(IndexInterface $index): bool;

    public function getIndexable(IndexInterface $index): bool;

    public function getIndexableName(IndexInterface $index, string $language): ?string;
}
