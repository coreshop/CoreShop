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

namespace CoreShop\Bundle\VariantBundle\EventListener;

use CoreShop\Component\Pimcore\DataObject\InheritanceHelper;
use CoreShop\Component\Resource\Exception\NiceValidationException;
use CoreShop\Component\Variant\Model\AttributeInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Event\DataObjectEvents;
use Pimcore\Event\Model\DataObjectEvent;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\Element\ValidationException;
use Pimcore\Tool;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ProductListener implements EventSubscriberInterface
{
    public function __construct(
        protected ValidatorInterface $validator,
        protected array $validationGroups,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataObjectEvents::PRE_ADD => ['preUpdate', 10],
            DataObjectEvents::PRE_UPDATE => ['preUpdate', 10],
        ];
    }

    public function preUpdate(DataObjectEvent $dataObjectEvent): void
    {
        /**
         * @var ProductVariantAwareInterface|null $object
         *
         * @psalm-var ProductVariantAwareInterface|null $object
         */
        $object = $dataObjectEvent->getObject();

        if (!$object instanceof ProductVariantAwareInterface) {
            return;
        }

        if (AbstractObject::OBJECT_TYPE_VARIANT !== $object->getType()) {
            return;
        }

        if (!$object->getPublished()) {
            return;
        }

        $this->validate($object);

        foreach (Tool::getValidLanguages() as $language) {
            $name = InheritanceHelper::useInheritedValues(static function () use ($object, $language) {
                return $object->getName($language);
            }, false);

            if (!$name) {
                $parent = $object->getVariantParent();

                if ($parent instanceof ProductVariantAwareInterface && $object->getAttributes()) {
                    $name = sprintf(
                        '%s %s',
                        $parent->getName($language),
                        implode(' ', array_map(static fn (AttributeInterface $a) => $a->getName($language), $object->getAttributes())),
                    );
                    $object->setName($name, $language);
                }
            }
        }
    }

    private function validate(ProductVariantAwareInterface $object)
    {
        $result = $this->validator->validate($object, null, $this->validationGroups);
        $validationExceptions = [];

        if (count($result) > 0) {
            $validationExceptions[] = new NiceValidationException(implode(
                \PHP_EOL,
                array_map(static function (ConstraintViolationInterface $violation) {
                    return $violation->getMessage();
                }, iterator_to_array($result)),
            ));

            throw new ValidationException(implode(\PHP_EOL, array_map(static function (ValidationException $exception) {
                return $exception->getMessage();
            }, $validationExceptions)));
        }
    }
}
