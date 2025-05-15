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

namespace CoreShop\Bundle\ResourceBundle\Controller;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ViewHandler implements ViewHandlerInterface
{
    public function __construct(
        private SerializerInterface $serializer,
    ) {
    }

    public function handle($data, array $options = []): JsonResponse
    {
        $context = SerializationContext::create();
        $context->setSerializeNull(true);

        if (array_key_exists('group', $options)) {
            $context->setGroups(['Default', $options['group']]);
        }

        return new JsonResponse($this->serializer->serialize($data, 'json', $context), 200, [], true);
    }
}
