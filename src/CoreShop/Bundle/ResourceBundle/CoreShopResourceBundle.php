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

namespace CoreShop\Bundle\ResourceBundle;

use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\DoctrineTargetEntitiesResolverPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterInstallersPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterPimcoreResourcesPass;
use CoreShop\Bundle\ResourceBundle\DependencyInjection\Compiler\RegisterResourcesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CoreShopResourceBundle extends Bundle
{
    const DRIVER_DOCTRINE_ORM = 'doctrine/orm';
    const DRIVER_DOCTRINE_PIMCORE = 'pimcore';

    const PIMCORE_MODEL_TYPE_OBJECT = 'object';
    const PIMCORE_MODEL_TYPE_FIELD_COLLECTION = 'fieldcollection';
    const PIMCORE_MODEL_TYPE_BRICK = 'brick';

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterResourcesPass());
        $container->addCompilerPass(new RegisterPimcoreResourcesPass());
        $container->addCompilerPass(new DoctrineTargetEntitiesResolverPass());
        $container->addCompilerPass(new RegisterInstallersPass());
    }

    /**
     * @return string[]
     */
    public static function getAvailableDrivers()
    {
        return [
            self::DRIVER_DOCTRINE_ORM,
        ];
    }
}
