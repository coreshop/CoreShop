<?php

namespace CoreShop\Component\Core\Pimcore;

use CoreShop\Component\Resource\Pimcore\Model\PimcoreModelInterface;
use Pimcore\Model\Object\Concrete;
use Pimcore\Model\Object\Service;
use Webmozart\Assert\Assert;

class ObjectService implements ObjectServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFolderByPath($path)
    {
        return Service::createFolderByPath($path);
    }

    public function copyObject(PimcoreModelInterface $from, PimcoreModelInterface $to)
    {
        /**
         * @var $from Concrete
         * @var $to Concrete
         */
        Assert::isInstanceOf($from, Concrete::class);
        Assert::isInstanceOf($to, Concrete::class);

        //load all in case of lazy loading fields
        $toFd = $to->getClass()->getFieldDefinitions();

        foreach ($toFd as $def) {
            $fromGetter = "get" . ucfirst($def->getName());
            $toSetter = "set" . ucfirst($def->getName());

            if (method_exists($from, $fromGetter) && method_exists($to, $toSetter)) {
                $to->$toSetter($from->$fromGetter());
            }
        }
    }
}
