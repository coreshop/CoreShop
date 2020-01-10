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

namespace CoreShop\Component\Pimcore\Routing;

use CoreShop\Component\Pimcore\DataObject\LinkGenerator as DataObjectLinkGenerator;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkGenerator implements LinkGeneratorInterface
{
    /**
     * @var DataObjectLinkGenerator
     */
    private $dataObjectLinkGenerator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param DataObjectLinkGenerator $dataObjectLinkGenerator
     * @param UrlGeneratorInterface   $urlGenerator
     */
    public function __construct(DataObjectLinkGenerator $dataObjectLinkGenerator, UrlGeneratorInterface $urlGenerator)
    {
        $this->dataObjectLinkGenerator = $dataObjectLinkGenerator;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($object, $routeName = null, $params = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($object instanceof Concrete && $this->dataObjectLinkGenerator->hasGenerator($object)) {
            $params['referenceType'] = $referenceType;
            $params['route'] = $routeName;

            return $this->dataObjectLinkGenerator->generate($object, $params);
        }

        return $this->urlGenerator->generate($routeName, $params, $referenceType);
    }
}
