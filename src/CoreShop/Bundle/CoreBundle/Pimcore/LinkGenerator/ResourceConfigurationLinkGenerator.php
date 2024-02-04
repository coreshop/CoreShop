<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Pimcore\LinkGenerator;

use CoreShop\Component\Pimcore\DataObject\AbstractSluggableLinkGenerator;
use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Pimcore\Exception\LinkGenerationNotPossibleException;
use CoreShop\Component\Resource\Metadata\RegistryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResourceConfigurationLinkGenerator extends AbstractSluggableLinkGenerator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private RegistryInterface $registry,
    ) {
    }

    public function generate(object $object, array $params = []): string
    {
        if (!$this->registry->hasClass($object::class)) {
            throw new LinkGenerationNotPossibleException();
        }

        $metadata = $this->registry->getByClass($object::class);

        if (!$metadata->hasParameter('route')) {
            throw new LinkGenerationNotPossibleException();
        }

        $routeName = $metadata->getParameter('route')['name'];
        $idParam = $metadata->getParameter('route')['id_param'];

        $locale = $params['_locale'] ?? null;

        $name = InheritanceHelper::useInheritedValues(static function () use ($object, $locale) {
            if (method_exists($object, 'getName')) {
                return $object->getName($locale);
            }

            return '';
        });

        $routeParams = [
            'name' => $this->slugify($name),
            $idParam => $object->getId(),
        ];

        if (isset($locale)) {
            $routeParams['_locale'] = $locale;
        }

        if (!isset($params['referenceType'])) {
            $params['referenceType'] = UrlGeneratorInterface::ABSOLUTE_PATH;
        }

        return $this->urlGenerator->generate($params['route'] ?? $routeName, $routeParams, $params['referenceType']);
    }
}
