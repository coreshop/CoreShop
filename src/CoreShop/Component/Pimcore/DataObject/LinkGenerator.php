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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

class LinkGenerator implements LinkGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate(Concrete $object, array $params = []): string
    {
        if ($linkGenerator = $object->getClass()->getLinkGenerator()) {
            return $linkGenerator->generate(
                $object,
                $params
            );
        }

        throw new \InvalidArgumentException(sprintf('Object %s with class %s has no Link Generator configured', $object->getId(), $object->getClassName()));
    }

    /**
     * {@inheritdoc}
     */
    public function hasGenerator(Concrete $object): bool
    {
        return $object->getClass()->getLinkGenerator() instanceof LinkGeneratorInterface;
    }
}