<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\SEO\Model;

interface SEOMetadataInterface
{
    /**
     * Updates the description.
     *
     * @param string $metaDescription
     */
    public function setMetaDescription(string $metaDescription): void;

    /**
     * Gets the description for the meta tag.
     *
     * @return string
     */
    public function getMetaDescription(): ?string;

    /**
     * Sets the original URL for content that has several URLs.
     *
     * @param string $originalUrl
     */
    public function setOriginalUrl(string $originalUrl): void;

    /**
     * Gets the original URL of this content.
     *
     * This will be used for the canonical link or to redirect to the original
     * URL, depending on your settings.
     *
     * @return string
     */
    public function getOriginalUrl(): ?string;

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle(string $title): void;

    /**
     * Gets the title.
     *
     * @return string
     */
    public function getTitle(): ?string;

    /**
     * @param array $extraProperties
     */
    public function setExtraProperties(array $extraProperties): void;

    /**
     * @param array $extraNames
     */
    public function setExtraNames(array $extraNames): void;

    /**
     * @param array $extraHttp
     */
    public function setExtraHttp(array $extraHttp): void;

    /**
     * @return array
     */
    public function getExtraProperties(): array;

    /**
     * @return array
     */
    public function getExtraNames(): array;

    /**
     * @return array
     */
    public function getExtraHttp(): array;

    /**
     * Add a key-value pair for meta attribute property.
     *
     * @param string $key
     * @param string $value
     */
    public function addExtraProperty(string $key, string $value): void;

    /**
     * Add a key-value pair for meta attribute name.
     *
     * @param string $key
     * @param string $value
     */
    public function addExtraName(string $key, string $value): void;

    /**
     * Add a key-value pair for meta attribute http-equiv.
     *
     * @param string $key
     * @param string $value
     */
    public function addExtraHttp(string $key, string $value): void;
}
