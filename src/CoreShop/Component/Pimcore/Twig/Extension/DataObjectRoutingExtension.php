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

namespace CoreShop\Component\Pimcore\Twig\Extension;

use Pimcore\Model\DataObject\Concrete;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataObjectRoutingExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('pimcore_object_path', [$this, 'objectPath']),
        ];
    }

    public function objectPath(Concrete $object, array $params = [])
    {
        $linkGenerator = $object->getClass()->getLinkGenerator();

        if (!$linkGenerator) {
            throw new \InvalidArgumentException('DataObject does not have a LinkGenerator');
        }

        return $linkGenerator->generate($object, $params);
    }
}
