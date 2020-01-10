<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\SEOBundle;

use CoreShop\Bundle\SEOBundle\DependencyInjection\Compiler\ExtractorRegistryServicePass;
use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class CoreShopSEOBundle extends AbstractPimcoreBundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ExtractorRegistryServicePass());
    }

    /**
     * {@inheritdoc}
     */
    public function getNiceName()
    {
        return 'CoreShop - SEO';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'CoreShop - SEO Bundle';
    }
}
