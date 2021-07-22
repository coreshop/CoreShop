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

interface PimcoreSEOAwareInterface extends SEOAwareInterface
{
    /**
     * @param string|null $language
     *
     * @return string
     */
    public function getPimcoreMetaTitle($language = null);

    /**
     * @param string $pimcoreMetaTitle
     * @param string $language
     */
    public function setPimcoreMetaTitle($pimcoreMetaTitle, $language = null);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getPimcoreMetaDescription($language = null);

    /**
     * @param string $pimcoreMetaDescription
     * @param string $language
     */
    public function setPimcoreMetaDescription($pimcoreMetaDescription, $language = null);
}
