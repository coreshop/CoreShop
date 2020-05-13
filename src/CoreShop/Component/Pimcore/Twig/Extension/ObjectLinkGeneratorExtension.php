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

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ObjectLinkGeneratorExtension extends AbstractExtension
{
    private $objectLinkGenerator;

    public function __construct(LinkGeneratorInterface $objectLinkGenerator)
    {
        $this->objectLinkGenerator = $objectLinkGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('object_link', function (Concrete $object, array $params = []) {
                return $this->objectLinkGenerator->generate($object);
            }),
        ];
    }
}
