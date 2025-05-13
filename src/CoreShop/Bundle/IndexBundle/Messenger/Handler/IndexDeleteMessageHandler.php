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

namespace CoreShop\Bundle\IndexBundle\Messenger\Handler;

use CoreShop\Bundle\IndexBundle\Messenger\IndexDeleteMessage;
use CoreShop\Component\Index\Service\IndexUpdaterServiceInterface;
use Pimcore\Model\DataObject\AbstractObject;

class IndexDeleteMessageHandler
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
