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

namespace CoreShop\Bundle\ResourceBundle\Pimcore;

use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

class CacheResourceMarshaller implements MarshallerInterface
{
    protected MarshallerInterface $defaultMarshaller;

    public function __construct(
        MarshallerInterface $defaultMarshaller = null,
    ) {
        $this->defaultMarshaller = $defaultMarshaller ?? new DefaultMarshaller();
    }

    public function marshall(array $values, ?array &$failed): array
    {
        foreach ($values as $data) {
            if ($data instanceof Concrete) {
                $class = $data->getClass();

                foreach ($class->getFieldDefinitions() as $fd) {
                    if (!$fd instanceof CacheMarshallerInterface) {
                        continue;
                    }

                    $data->setObjectVar(
                        $fd->getName(),
                        $fd->marshalForCache($data, $data->getObjectVar($fd->getName())),
                    );
                }
            }
        }

        return $this->defaultMarshaller->marshall($values, $failed);
    }

    public function unmarshall(string $value): mixed
    {
        $data = $this->defaultMarshaller->unmarshall($value);

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
}
