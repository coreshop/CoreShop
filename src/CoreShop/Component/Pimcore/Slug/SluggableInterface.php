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

namespace CoreShop\Component\Pimcore\Slug;

use Pimcore\Model\DataObject\Data\UrlSlug;

interface SluggableInterface
{
    /**
     * @return int|string|null
     */
    public function getId();

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return UrlSlug[]
     */
    public function getSlug($language = null): ?array;

    /**
     * @param UrlSlug[] $slug
     */
    public function setSlug(?array $slug, $language = null);

    public function getNameForSlug(string $language = null): ?string;
}
