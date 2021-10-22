<?php
/**
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

namespace CoreShop\Component\SEO\Model;

/**
 * @psalm-suppress MissingConstructor
 */
class SEOMetadata implements SEOMetadataInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $originalUrl;

    /**
     * @var string
     */
    private $metaDescription;

    /**
     * @var string
     */
    private $metaKeywords;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $extraProperties = [];

    /**
     * @var array
     */
    private $extraNames = [];

    /**
     * @var array
     */
    private $extraHttp = [];

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return mixed
     */
    public function getLocale()
    {
        return $this->locale;
    }

    public function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaKeywords(string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->metaKeywords;
    }

    public function setOriginalUrl(string $originalUrl): void
    {
        $this->originalUrl = $originalUrl;
    }

    public function getOriginalUrl(): ?string
    {
        return $this->originalUrl;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setExtraProperties(array $extraProperties): void
    {
        $this->extraProperties = $this->toArray($extraProperties);
    }

    public function getExtraProperties(): array
    {
        return $this->extraProperties;
    }

    public function addExtraProperty(string $key, string $value): void
    {
        $this->extraProperties[$key] = $value;
    }

    public function removeExtraProperty(string $key)
    {
        if (array_key_exists($key, $this->extraProperties)) {
            unset($this->extraProperties[$key]);
        }
    }

    public function setExtraNames(array $extraNames): void
    {
        $this->extraNames = $this->toArray($extraNames);
    }

    public function getExtraNames(): array
    {
        return $this->extraNames;
    }

    public function addExtraName(string $key, string $value): void
    {
        $this->extraNames[$key] = $value;
    }

    public function removeExtraName($key): void
    {
        if (array_key_exists($key, $this->extraNames)) {
            unset($this->extraNames[$key]);
        }
    }

    public function setExtraHttp(array $extraHttp): void
    {
        $this->extraHttp = $this->toArray($extraHttp);
    }

    public function getExtraHttp(): array
    {
        return $this->extraHttp;
    }

    public function addExtraHttp(string $key, string $value): void
    {
        $this->extraHttp[$key] = $value;
    }

    public function removeExtraHttp(string $key): void
    {
        if (array_key_exists($key, $this->extraHttp)) {
            unset($this->extraHttp[$key]);
        }
    }

    /**
     * @param mixed $data
     */
    private function toArray($data): array
    {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof \Traversable) {
            return iterator_to_array($data);
        }

        throw new \InvalidArgumentException(sprintf('Expected array or Traversable, got "%s"', get_debug_type($data)));
    }
}
