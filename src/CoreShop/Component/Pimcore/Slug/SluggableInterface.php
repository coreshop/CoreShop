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

namespace CoreShop\Component\Pimcore\Slug;

use Pimcore\Model\DataObject\Data\UrlSlug;

interface SluggableInterface
{
    public function getId(): ?int;

    public function getKey(): ?string;

    /**
     * @return UrlSlug[]|null
     */
    public function getSlug(?string $language = null): ?array;

    /**
     * @param UrlSlug[] $slug
     */
    public function setSlug(array $slug, ?string $language = null);

    public function getNameForSlug(?string $language = null): ?string;
}
