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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

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
