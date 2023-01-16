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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\IndexBundle\Messenger\Handler;

use CoreShop\Bundle\IndexBundle\Messenger\IndexDeleteMessage;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class IndexDeleteMessageHandler implements MessageHandlerInterface
{
    private array $validObjectTypes = [AbstractObject::OBJECT_TYPE_OBJECT, AbstractObject::OBJECT_TYPE_VARIANT];

    public function __construct(
        private IndexUpdaterServiceInterface $indexUpdaterService,
    ) {
    }

    public function __invoke(IndexDeleteMessage $indexDeleteMessage)
    {
        $this->indexUpdaterService->removeFromIndicesById(
            $indexDeleteMessage->getClassName(),
            $indexDeleteMessage->getIndexableId(),
        );
    }
}
