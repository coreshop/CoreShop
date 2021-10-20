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

interface SEOMetadataInterface
{
    /**
     * Updates the description.
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
     */
    public function setTitle(string $title): void;

    /**
     * Gets the title.
     *
     * @return string
     */
    public function getTitle(): ?string;

    public function setExtraProperties(array $extraProperties): void;

    public function setExtraNames(array $extraNames): void;

    public function setExtraHttp(array $extraHttp): void;

    public function getExtraProperties(): array;

    public function getExtraNames(): array;

    public function getExtraHttp(): array;

    /**
     * Add a key-value pair for meta attribute property.
     */
    public function addExtraProperty(string $key, string $value): void;

    /**
     * Add a key-value pair for meta attribute name.
     */
    public function addExtraName(string $key, string $value): void;

    /**
     * Add a key-value pair for meta attribute http-equiv.
     */
    public function addExtraHttp(string $key, string $value): void;
}
