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

namespace CoreShop\Bundle\CoreBundle;

use CoreShop\Bundle\CoreBundle\Application\Version;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterPortletsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterProductHelperPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterReportsPass;
use CoreShop\Bundle\CoreBundle\DependencyInjection\Compiler\TranslatableEntityLocalePass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopCoreBundle extends AbstractResourceBundle implements PimcoreBundleInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatableEntityLocalePass());
        $container->addCompilerPass(new RegisterProductHelperPass());
        $container->addCompilerPass(new RegisterReportsPass());
        $container->addCompilerPass(new RegisterPortletsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'CoreShop\Component\Core\Model';
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName()
    {
        return 'CoreShop - Core';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'CoreShop - Pimcore eCommerce';
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return Version::getVersion() . " (" . $this->getComposerVersion() . ")";
    }

    /**
     * @return string
     */
    public function getComposerVersion()
    {
        $version = Versions::getVersion('coreshop/core-shop');

        return $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstaller()
    {
        return $this->container->get(Installer::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdminIframePath()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getCssPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getEditmodeJsPaths()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getEditmodeCssPaths()
    {
        return [];
    }
}
