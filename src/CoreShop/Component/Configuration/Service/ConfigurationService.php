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

namespace CoreShop\Component\Configuration\Service;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Configuration\Repository\ConfigurationRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ConfigurationService implements ConfigurationServiceInterface
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ConfigurationRepositoryInterface $configurationRepository,
        protected FactoryInterface $configurationFactory,
    ) {
    }

    public function get(string $key, bool $returnObject = false): mixed
    {
        $config = $this->configurationRepository->findByKey($key);

        if ($config instanceof ConfigurationInterface) {
            return $returnObject ? $config : $config->getData();
        }

        return null;
    }

    public function set(string $key, mixed $data): ConfigurationInterface
    {
        $config = $this->get($key, true);

        if (!$config) {
            $config = $this->configurationFactory->createNew();
            $config->setKey($key);
        }

        $config->setData($data);
        $this->entityManager->persist($config);
        $this->entityManager->flush();

        return $config;
    }

    public function remove(string $key): void
    {
        $config = $this->get($key, true);

        if ($config instanceof ConfigurationInterface) {
            $this->entityManager->remove($config);
            $this->entityManager->flush();
        }
    }
}
