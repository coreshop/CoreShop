<?php

namespace CoreShop\Component\Core\Configuration;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Core\Repository\ConfigurationRepositoryInterface;
use CoreShop\Component\Core\Model\StoreInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use \CoreShop\Component\Configuration\Service\ConfigurationService as BaseConfigurationService;
use CoreShop\Component\Store\Context\StoreContextInterface;
use Doctrine\ORM\EntityManagerInterface;

class ConfigurationService extends BaseConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @var StoreContextInterface
     */
    protected $storeContext;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ConfigurationRepositoryInterface $configurationRepository
     * @param FactoryInterface $configurationFactory
     * @param StoreContextInterface $storeContext
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ConfigurationRepositoryInterface $configurationRepository,
        FactoryInterface $configurationFactory,
        StoreContextInterface $storeContext
    )
    {
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

        if (is_null($config)) {
            $config = $this->configurationRepository->findBy(["key" => $key, "store" => null]);

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
    protected function getStore() {
        try {
            //TODO: Check for frontend calls, but how? Tool::isFrontend is not good at all :/
            
            return $this->storeContext->getStore();
        } catch(\Exception $ex) {}

        return null;
    }
}