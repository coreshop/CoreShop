<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\SEO\Model;

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
    public function setLocale($locale)
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

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($metaKeywords)
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * {@inheritdoc}
     */
    public function setOriginalUrl($originalUrl)
    {
        $this->originalUrl = $originalUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalUrl()
    {
        return $this->originalUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtraProperties($extraProperties)
    {
        $this->extraProperties = $this->toArray($extraProperties);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraProperties()
    {
        return $this->extraProperties;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtraProperty($key, $value)
    {
        $this->extraProperties[$key] = (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExtraProperty($key)
    {
        if (array_key_exists($key, $this->extraProperties)) {
            unset($this->extraProperties[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setExtraNames($extraNames)
    {
        $this->extraNames = $this->toArray($extraNames);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraNames()
    {
        return $this->extraNames;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtraName($key, $value)
    {
        $this->extraNames[$key] = (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExtraName($key)
    {
        if (array_key_exists($key, $this->extraNames)) {
            unset($this->extraNames[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setExtraHttp($extraHttp)
    {
        $this->extraHttp = $this->toArray($extraHttp);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraHttp()
    {
        return $this->extraHttp;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtraHttp($key, $value)
    {
        $this->extraHttp[$key] = (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    public function removeExtraHttp($key)
    {
        if (array_key_exists($key, $this->extraHttp)) {
            unset($this->extraHttp[$key]);
        }
    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    private function toArray($data)
    {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof \Traversable) {
            return iterator_to_array($data);
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Expected array or Traversable, got "%s"',
                is_object($data) ? get_class($data) : gettype($data)
            )
        );
    }
}
