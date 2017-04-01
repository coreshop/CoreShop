<?php

namespace CoreShop\Bundle\ConfigurationBundle\Helper;

use CoreShop\Component\Configuration\Model\ConfigurationInterface;
use CoreShop\Component\Resource\Factory\FactoryInterface;
use CoreShop\Component\Core\Repository\RepositoryInterface;

class ConfigurationHelper implements ConfigurationHelperInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * this is a small per request cache to know which configuration is which is, this info is used in self::getByKey().
     *
     * @var array
     */
    private static $nameIdMappingCache = [];

    /**
     * @param FactoryInterface $factory
     * @param RepositoryInterface $repository
     */
    public function __construct(FactoryInterface $factory, RepositoryInterface $repository)
    {
        $this->factory = $factory;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function isMultiStoreEnabled() {
         if (\Zend_Registry::isRegistered('coreshop_multishop_enabled')) {
            return \Zend_Registry::get('coreshop_multishop_enabled');
        } else {
            $multiShop = intval($this->get('SYSTEM.MULTISHOP.ENABLED')) === 1;

            if (is_bool($multiShop)) {
                \Zend_Registry::set('coreshop_multishop_enabled', $multiShop);

                return $multiShop;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $storeId = null, $returnObject = false)
    {
        $cacheKey = $key . '~~~' . ($storeId ? $storeId : '-');

        /*if (Tool::isFrontend()) {
            if (!in_array($key, self::$systemKeys)) {
                if (self::multiShopEnabled()) {
                    if (is_null($storeId)) {
                        $storeId = Shop::getShop()->getId();
                    }
                }
            }
        }*/

        // check if pimcore already knows the id for this $name, if yes just return it
        if (array_key_exists($cacheKey, self::$nameIdMappingCache)) {
            $entry = $this->repository->getById(self::$nameIdMappingCache[$cacheKey]);

            if ($returnObject) {
                return $entry;
            }

            return $entry instanceof self ? $entry->getData() : null;
        }

        // create a tmp object to obtain the id
        $configurationEntry = $this->factory->createNew();

        try {
            $configurationEntry->getDao()->getByKey($key, $storeId);
        } catch (\Exception $e) {
            return null; //return silently.
        }

        // to have a singleton in a way. like all instances of Element\ElementInterface do also, like Object\AbstractObject
        if ($configurationEntry->getId() > 0) {
            // add it to the mini-per request cache
            self::$nameIdMappingCache[$cacheKey] = $configurationEntry->getId();
            $entry = $this->repository->getById($configurationEntry->getId());

            if ($returnObject) {
                return $entry;
            }

            return $entry instanceof ConfigurationInterface ? $entry->getData() : null;
        }

        return null; //Should not happen
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $data, $storeId = null)
    {
        $configEntry = $this->get($key, $storeId, true);

        if (!$configEntry) {
            $configEntry = $this->factory->createNew();
            $configEntry->setKey($key);
        }

        $configEntry->setShopId($storeId);
        $configEntry->setData($data);
        $configEntry->save();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $list = $this->repository->getList();
        $list->setFilter(function ($row) use ($key) {
            if ($row['key'] == $key) {
                return true;
            }

            return false;
        });

        $configurations = $list->getData();

        if (is_array($configurations)) {
            foreach ($configurations as $config) {
                $config->delete();
            }
        }
    }
}