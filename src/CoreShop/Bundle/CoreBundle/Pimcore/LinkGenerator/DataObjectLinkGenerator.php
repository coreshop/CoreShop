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

namespace CoreShop\Bundle\CoreBundle\Pimcore\LinkGenerator;

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Pimcore\DataObject\AbstractSluggableLinkGenerator;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DataObjectLinkGenerator extends AbstractSluggableLinkGenerator
{
    public function __construct(private string $type, private string $routeName, private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function generate(Concrete $object, array $params = []): string
    {
        $locale = $params['_locale'] ?? null;

        $name = InheritanceHelper::useInheritedValues(function () use ($object, $locale) {
            if (method_exists($object, 'getName')) {
                return $object->getName($locale);
            }

            return '';
        });

        $routeParams = [
            'name' => $this->slugify($name),
            $this->type => $object->getId(),
        ];

        if (isset($locale)) {
            $routeParams['_locale'] = $locale;
        }

        if (!isset($params['referenceType'])) {
            $params['referenceType'] = UrlGeneratorInterface::ABSOLUTE_PATH;
        }

        return $this->urlGenerator->generate($params['route'] ?? $this->routeName, $routeParams, $params['referenceType']);
    }
}
