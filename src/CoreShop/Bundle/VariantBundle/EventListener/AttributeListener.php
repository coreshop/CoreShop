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

namespace CoreShop\Bundle\VariantBundle\EventListener;

use CoreShop\Component\Resource\Exception\NiceValidationException;
use CoreShop\Component\Variant\Model\AttributeGroupInterface;
use CoreShop\Component\Variant\Model\AttributeInterface;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AttributeListener implements EventSubscriberInterface
{
    public function __construct(protected ValidatorInterface $validator, protected array $validationGroups)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::PRE_UPDATE => 'preUpdate',
            DataObjectEvents::PRE_ADD => 'preUpdate',
        ];
    }

    public function preUpdate(DataObjectEvent $dataObjectEvent): void
    {
        $object = $dataObjectEvent->getObject();

        /**
         * @var AttributeInterface $object
         */
        if (!$object instanceof AttributeInterface) {
            return;
        }

        $parent = $object->getParent();

        if ($parent instanceof AttributeGroupInterface) {
            $object->setAttributeGroup($parent);
        } else {
            $object->setAttributeGroup(null);
        }

        $this->validate($object);
    }

    private function validate(AttributeInterface $object)
    {
        $result = $this->validator->validate($object, null, $this->validationGroups);
        $validationExceptions = [];

        if (count($result) > 0) {
            $validationExceptions[] = new NiceValidationException(implode(
                PHP_EOL,
                array_map(static function (ConstraintViolationInterface $violation) {
                    return $violation->getMessage();
                }, iterator_to_array($result))
            ));

            throw new ValidationException(implode(PHP_EOL, array_map(static function (ValidationException $exception) { return $exception->getMessage(); }, $validationExceptions)));
        }
    }
}
