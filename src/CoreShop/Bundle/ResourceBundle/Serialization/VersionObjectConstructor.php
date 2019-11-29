<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\ResourceBundle\Serialization;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;

class VersionObjectConstructor implements ObjectConstructorInterface
{
    /**
     * @var ObjectConstructorInterface
     */
    protected $fallbackConstructor;

    /**
     * @var ObjectConstructorInterface
     */
    protected $decorated;

    public function __construct(ObjectConstructorInterface $fallbackConstructor, ObjectConstructorInterface $decorated)
    {
        $this->fallbackConstructor = $fallbackConstructor;
        $this->decorated = $decorated;
    }

    public function construct(
        VisitorInterface $visitor,
        ClassMetadata $metadata,
        $data,
        array $type,
        DeserializationContext $context
    ) {
        if ($context->getAttribute('unmarshalVersion')) {
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        return $this->decorated->construct($visitor, $metadata, $data, $type, $context);
    }
}
