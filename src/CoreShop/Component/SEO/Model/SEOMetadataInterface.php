<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
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
    public function setMetaDescription($metaDescription);

    /**
     * Gets the description for the meta tag.
     *
     * @return string
     */
    public function getMetaDescription();

    /**
     * Sets the original URL for content that has several URLs.
     *
     * @param string $originalUrl
     */
    public function setOriginalUrl($originalUrl);

    /**
     * Gets the original URL of this content.
     *
     * This will be used for the canonical link or to redirect to the original
     * URL, depending on your settings.
     *
     * @return string
     */
    public function getOriginalUrl();

    /**
     * Sets the title.
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Gets the title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * @param array
     */
    public function setExtraProperties($extraProperties);

    /**
     * @param array
     */
    public function setExtraNames($extraNames);

    /**
     * @param array
     */
    public function setExtraHttp($extraHttp);

    /**
     * @return array
     */
    public function getExtraProperties();

    /**
     * @return array
     */
    public function getExtraNames();

    /**
     * @return array
     */
    public function getExtraHttp();

    /**
     * Add a key-value pair for meta attribute property.
     *
     * @param string $key
     * @param string $value
     */
    public function addExtraProperty($key, $value);

    /**
     * Add a key-value pair for meta attribute name.
     *
     * @param string $key
     * @param string $value
     */
    public function addExtraName($key, $value);

    /**
     * Add a key-value pair for meta attribute http-equiv.
     *
     * @param string $key
     * @param string $value
     */
    public function addExtraHttp($key, $value);
}