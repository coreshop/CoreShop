<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Resource\Pimcore;

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
        /*
         * @var $from Concrete
         * @var $to Concrete
         */
        Assert::isInstanceOf($from, Concrete::class);
        Assert::isInstanceOf($to, Concrete::class);

        //load all in case of lazy loading fields
        $toFd = $to->getClass()->getFieldDefinitions();

        foreach ($toFd as $def) {
            $fromGetter = 'get'.ucfirst($def->getName());
            $toSetter = 'set'.ucfirst($def->getName());

            if (method_exists($from, $fromGetter) && method_exists($to, $toSetter)) {
                $to->$toSetter($from->$fromGetter());
            }
        }
    }
}
