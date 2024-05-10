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

namespace CoreShop\Component\Pimcore\Event;

use CoreShop\Component\Pimcore\Slug\SluggableInterface;
use Pimcore\Model\Site;
use Symfony\Contracts\EventDispatcher\Event;

class SlugGenerationEvent extends Event
{
    public function __construct(
        protected SluggableInterface $sluggable,
        protected string $slug,
        protected ?string $suffix = null,
        protected ?Site $site = null,
        protected ?string $locale = null,
    ) {
    }

    public function getSluggable(): SluggableInterface
    {
        return $this->sluggable;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }
}
