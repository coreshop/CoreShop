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

namespace CoreShop\Bundle\AdminBundle;

use CoreShop\Bundle\CoreBundle\Application\Version;
use PackageVersions\Versions;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;

/**
 * @deprecated Don't use anymore, Responsability of this has been moved to CoreBundle instead
 * will be removed with beta-1
 */
final class CoreShopAdminBundle extends AbstractPimcoreBundle
{
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
        return 'CoreShop';
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
}
