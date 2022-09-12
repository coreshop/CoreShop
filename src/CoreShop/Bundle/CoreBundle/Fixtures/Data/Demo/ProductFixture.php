<?php
declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Bundle\CoreBundle\Fixtures\Data\Demo;

use Doctrine\Persistence\ObjectManager;

class ProductFixture extends AbstractProductFixture
{
    public function load(ObjectManager $manager): void
    {
        if (!count($this->container->get('coreshop.repository.product')->findAll())) {
            $productsCount = 25;

            for ($i = 0; $i < $productsCount; ++$i) {
                $product = $this->createProduct('products');

                $product->save();
            }
        }
    }
}
