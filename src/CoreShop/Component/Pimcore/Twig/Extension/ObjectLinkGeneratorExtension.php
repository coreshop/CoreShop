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

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

final class ObjectLinkGeneratorExtension extends \Twig_Extension
{
    /**
     * @var LinkGeneratorInterface
     */
    private $objectLinkGenerator;

    /**
     * @param LinkGeneratorInterface $objectLinkGenerator
     */
    public function __construct(LinkGeneratorInterface $objectLinkGenerator)
    {
        $this->objectLinkGenerator = $objectLinkGenerator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_Function('object_link', function (Concrete $object, array $params = []) {
                return $this->objectLinkGenerator->generate($object);
            }),
        ];
    }
}
