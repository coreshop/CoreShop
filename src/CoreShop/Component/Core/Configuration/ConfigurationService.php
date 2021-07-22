<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Core\Configuration;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Configuration\Service\ConfigurationService as BaseConfigurationService;
use CoreShop\Component\Core\Repository\ConfigurationRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Store\Context\StoreContextInterface;
use CoreShop\Component\Store\Context\StoreNotFoundException;
use CoreShop\Component\Store\Model\StoreInterface;
use Doctrine\ORM\EntityManagerInterface;

class ConfigurationService extends BaseConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @var StoreContextInterface
     */
    protected $storeContext;

    /**
     * @var ConfigurationRepositoryInterface
     */
    protected $configurationRepository;

    /**
     * @param EntityManagerInterface           $entityManager
     * @param ConfigurationRepositoryInterface $configurationRepository
     * @param FactoryInterface                 $configurationFactory
     * @param StoreContextInterface            $storeContext
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationRepositoryInterface $configurationRepository,
        FactoryInterface $configurationFactory,
        StoreContextInterface $storeContext
    ) {
        parent::__construct($entityManager, $configurationRepository, $configurationFactory);

        $this->storeContext = $storeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getForStore($key, StoreInterface $store = null, $returnObject = false)
    {
        if (null === $store) {
            $store = $this->getStore();
        }

        $config = $this->configurationRepository->findForKeyAndStore($key, $store);

        if (null === $config) {
            $config = $this->configurationRepository->findBy(['key' => $key, 'store' => null]);

            if (is_array($config) && count($config) > 0) {
                $config = $config[0];
            }
        }

        if ($config instanceof ConfigurationInterface) {
            return $returnObject ? $config : $config->getData();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setForStore($key, $data, StoreInterface $store = null)
    {
        if (null === $store) {
            $store = $this->getStore();
        }

        $config = $this->getForStore($key, $store, true);

        if (!$config) {
            $config = $this->configurationFactory->createNew();
            $config->setKey($key);
            $config->setStore($store);
        }

        $config->setData($data);
        $config->setStore($store);
        $this->entityManager->persist($config);
        $this->entityManager->flush();

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function removeForStore($key, StoreInterface $store = null)
    {
        if (null === $store) {
            $store = $this->getStore();
        }

        $config = $this->getForStore($key, $store, true);

        if ($config instanceof ConfigurationInterface) {
            $this->entityManager->remove($config);
            $this->entityManager->flush();
        }
    }

    /**
     * @return \CoreShop\Component\Store\Model\StoreInterface|null
     */
    protected function getStore()
    {
        try {
            return $this->storeContext->getStore();
        } catch (StoreNotFoundException $ex) {
            //if we don't have a store, do nothing and return false
        }

        return null;
    }
}
