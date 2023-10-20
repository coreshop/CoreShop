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

namespace CoreShop\Bundle\ResourceBundle\Cache;

use CoreShop\Bundle\ResourceBundle\Pimcore\CacheMarshallerInterface;
use Pimcore\Cache\Core\CoreCacheHandler;
use Pimcore\Model\DataObject\Concrete;

/**
 * @psalm-suppress InternalClass
 */
class CoreCacheHandlerDecorator extends CoreCacheHandler
{
    public function load($key): mixed
    {
        /**
         * @psalm-suppress InternalMethod
         */
        $data = parent::load($key);

        if ($data instanceof Concrete) {
            $class = $data->getClass();

            foreach ($class->getFieldDefinitions() as $fd) {
                if (!$fd instanceof CacheMarshallerInterface) {
                    continue;
                }

                $data->setObjectVar(
                    $fd->getName(),
                    $fd->unmarshalForCache($data, $data->getObjectVar($fd->getName())),
                );
            }
        }

        return $data;
    }

    protected function prepareCacheData($data): mixed
    {
        /**
         * It is useless to prepare the data for cache here
         * since the Pimcore CoreCacheHandler on line 502 loads
         * the data-object completely fresh, so we still have to use
         * DeepCopy approach
         */
        return $data;
    }
}
