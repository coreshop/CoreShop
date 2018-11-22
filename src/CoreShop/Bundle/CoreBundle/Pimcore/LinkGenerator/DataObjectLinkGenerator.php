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

namespace CoreShop\Bundle\CoreBundle\Pimcore\LinkGenerator;

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Pimcore\DataObject\AbstractSluggableLinkGenerator;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

class DataObjectLinkGenerator extends AbstractSluggableLinkGenerator
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param string $type
     * @param string $routeName
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(string $type, string $routeName, UrlGeneratorInterface $urlGenerator)
    {
        $this->type = $type;
        $this->routeName = $routeName;
        $this->urlGenerator = $urlGenerator;
    }

    public function generate(Concrete $object, array $params = []): string
    {
        /**
         * @var Concrete $object
         */
        Assert::isInstanceOf($object, Concrete::class);

        $locale = isset($params['_locale']) ? $params['_locale'] : null;

        $name = InheritanceHelper::useInheritedValues(function () use ($object, $locale) {
            if (method_exists($object, 'getName')) {
                return $object->getName($locale);
            }

            return '';
        });

        $routeParams = [
            'name' => $this->slugify($name),
            $this->type => $object->getId()
        ];

        if (isset($locale)) {
            $routeParams['_locale'] = $locale;
        }

        if (!isset($params['referenceType'])) {
            $params['referenceType'] = UrlGeneratorInterface::ABSOLUTE_PATH;
        }

        return $this->urlGenerator->generate($params['route'] ?: $this->routeName, $routeParams, $params['referenceType']);
    }
}