<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

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
