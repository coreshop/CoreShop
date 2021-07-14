<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\PayumPaymentBundle;

use CoreShop\Bundle\PaymentBundle\CoreShopPaymentBundle;
use CoreShop\Bundle\PayumPaymentBundle\DependencyInjection\Compiler\RegisterGatewayConfigTypePass;
use CoreShop\Bundle\PayumPaymentBundle\DependencyInjection\Compiler\RegisterPaymentSettingsFormsPass;
use CoreShop\Bundle\ResourceBundle\AbstractResourceBundle;
use CoreShop\Bundle\ResourceBundle\CoreShopResourceBundle;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\PimcoreBundleInterface;
use Pimcore\HttpKernel\BundleCollection\BundleCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CoreShopPayumPaymentBundle extends AbstractResourceBundle implements PimcoreBundleInterface
{
    public function getSupportedDrivers()
    {
        return [
            CoreShopResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public static function registerDependentBundles(BundleCollection $collection)
    {
        parent::registerDependentBundles($collection);

        $collection->addBundle(new CoreShopPaymentBundle(), 2200);
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterGatewayConfigTypePass());
        $container->addCompilerPass(new RegisterPaymentSettingsFormsPass());
    }

    protected function getModelNamespace()
    {
        return 'CoreShop\Component\PayumPayment\Model';
    }

    public function getNiceName()
    {
        return 'CoreShop - Payum Payment';
    }

    public function getDescription()
    {
        return 'CoreShop - Payum Payment Bundle';
    }

    public function getInstaller()
    {
        return null;
    }

    public function getAdminIframePath()
    {
        return null;
    }

    public function getJsPaths()
    {
        return [];
    }

    public function getCssPaths()
    {
        return [];
    }

    public function getEditmodeJsPaths()
    {
        return [];
    }

    public function getEditmodeCssPaths()
    {
        return [];
    }
}
