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

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\Slug;

use Pimcore\Http\Request\Resolver\SiteResolver;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\HttpFoundation\RequestStack;

class SluggableLinkGenerator implements LinkGeneratorInterface
{
    private SiteResolver $siteResolver;
    private RequestStack $requestStack;

    public function __construct(SiteResolver $siteResolver, RequestStack $requestStack)
    {
        $this->siteResolver = $siteResolver;
        $this->requestStack = $requestStack;
    }

    public function generate(Concrete $object, array $params = []): string
    {
        if (!$object instanceof SluggableInterface) {
            throw new \InvalidArgumentException(sprintf('Object with Path "%s" must implement %s',
                $object->getFullPath(), SluggableInterface::class));
        }

        $slugs = $object->getSlug($params['_locale'] ?? null);
        $slug = null;
        $site = $params['site'] ?? (
            $this->requestStack->getMasterRequest() ?
                $this->siteResolver->getSite($this->requestStack->getMasterRequest()) :
                null
            );

        foreach ($slugs as $possibleSlug) {
            if ($possibleSlug->getSiteId() === ($site ? $site->getId() : 0)) {
                $slug = $possibleSlug;
                break;
            }
        }

        if (null === $slug) {
            throw new \InvalidArgumentException(sprintf('No Valid Slug found for object "%s"', $object->getFullPath()));
        }

        return $slug->getSlug();
    }
}
