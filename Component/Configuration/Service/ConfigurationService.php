<?php

namespace CoreShop\Component\Configuration\Service;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Configuration\Repository\ConfigurationRepositoryInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ConfigurationRepositoryInterface
     */
    protected $configurationRepository;

    /**
     * @var FactoryInterface
     */
    protected $configurationFactory;

    /**
     * @param EntityManagerInterface $entityManager
     * @param ConfigurationRepositoryInterface $configurationRepository
     * @param FactoryInterface $configurationFactory
     */
    public function __construct(EntityManagerInterface $entityManager, ConfigurationRepositoryInterface $configurationRepository, FactoryInterface $configurationFactory)
    {
        $this->entityManager = $entityManager;
        $this->configurationRepository = $configurationRepository;
        $this->configurationFactory = $configurationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $returnObject = false)
    {
        $config = $this->configurationRepository->findByKey($key);

        if ($config instanceof ConfigurationInterface) {
            return $returnObject ? $config : $config->getData();
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data)
    {
        $config = $this->get($key, true);

        if (!$config) {
            $config = $this->configurationFactory->createNew();
            $config->setKey($key);
        }

        $config->setData($data);
        $this->entityManager->persist($config);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $config = $this->get($key, true);

        if ($config instanceof ConfigurationInterface) {
            $this->entityManager->remove($config);
            $this->entityManager->flush();
        }
    }
}