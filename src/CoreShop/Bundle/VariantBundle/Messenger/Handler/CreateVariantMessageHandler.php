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

namespace CoreShop\Bundle\VariantBundle\Messenger\Handler;

use CoreShop\Bundle\VariantBundle\Messenger\CreateVariantMessage;
use CoreShop\Bundle\VariantBundle\Service\VariantGeneratorServiceInterface;
use CoreShop\Component\Variant\Model\ProductVariantAwareInterface;
use Pimcore\Model\DataObject;
use Pimcore\Model\Notification\Service\NotificationService;

class CreateVariantMessageHandler
{
    public function __construct(
        protected VariantGeneratorServiceInterface $variantGeneratorService,
        protected NotificationService $notificationService,
    ) {
    }

    public function __invoke(CreateVariantMessage $message)
    {
        $object = DataObject::getById($message->getObjectId());

        if (!$object instanceof ProductVariantAwareInterface) {
            return;
        }

        $attributeIds = $message->getAttributeIds();
        if (!$attributeIds) {
            return;
        }

        $variant = $this->variantGeneratorService->generateVariant($attributeIds, $object);

        if (null !== $variant && null !== $message->getUserId()) {
            /**
             * @psalm-suppress InternalMethod
             */
            $this->notificationService->sendToUser(
                $message->getUserId(),
                0,
                sprintf('Variant %s generated', $variant->getName()),
                sprintf(
                    'Variant %s with ID %s for Product %s with ID %s has been generated',
                    $variant->getName(),
                    $variant->getId(),
                    $object->getKey(),
                    $object->getId(),
                ),
            );
        }
    }
}
