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

namespace CoreShop\Component\SEO\Generator;

use CoreShop\Component\SEO\Model\SEOAwareInterface;

interface HeadMetaGeneratorInterface
{
    /**
     * @return string
     */
    public function getTitlePosition();

    /**
     * @param SEOAwareInterface $seoObject
     *
     * @return string
     */
    public function generateTitle(SEOAwareInterface $seoObject);

    /**
     * @param SEOAwareInterface $seoObject
     *
     * @return string
     */
    public function generateDescription(SEOAwareInterface $seoObject);

    /**
     * @param SEOAwareInterface $seoObject
     *
     * @return array
     */
    public function generateMeta(SEOAwareInterface $seoObject);
}