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

namespace CoreShop\Bundle\ResourceBundle\Pimcore;

use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;

class CacheResourceMarshaller implements MarshallerInterface
{
    protected MarshallerInterface $defaultMarshaller;
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, MarshallerInterface $defaultMarshaller = null)
    {
        $this->defaultMarshaller = $defaultMarshaller ?? new DefaultMarshaller();
        $this->entityManager = $entityManager;
    }

    public function marshall(array $values, ?array &$failed): array
    {
        foreach ($values as $data) {
            if ($data instanceof Concrete) {
                $class = $data->getClass();

                if (!$class) {
                    continue;
                }

                /**
                 * @var Data&CacheMarshallerInterface|null $fd
                 * @psalm-var Data&CacheMarshallerInterface|null $fd
                 */
                foreach ($class->getFieldDefinitions() as $fd) {
                    if (!$fd instanceof CacheMarshallerInterface) {
                        continue;
                    }

                    if (!$fd instanceof Data) {
                        continue;
                    }

                    $data->setObjectVar(
                        $fd->getName(),
                        $fd->marshalForCache($data, $data->getObjectVar($fd->getName()))
                    );
                }
            }
        }

        return $this->defaultMarshaller->marshall($values, $failed);
    }

    public function unmarshall(string $value)
    {
        $data = $this->defaultMarshaller->unmarshall($value);

        if ($data instanceof Concrete) {
            $class = $data->getClass();

            if (!$class) {
                return $data;
            }

            /**
             * @var Data&CacheMarshallerInterface       $fd
             * @psalm-var Data&CacheMarshallerInterface $fd
             */
            foreach ($class->getFieldDefinitions() as $fd) {
                if (!$fd instanceof CacheMarshallerInterface) {
                    continue;
                }

                if (!$fd instanceof Data) {
                    continue;
                }

                $data->setObjectVar(
                    $fd->getName(),
                    $fd->unmarshalForCache($data, $data->getObjectVar($fd->getName()))
                );
            }
        }

        return $data;
    }
}
