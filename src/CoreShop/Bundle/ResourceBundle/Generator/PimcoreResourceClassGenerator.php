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

namespace CoreShop\Bundle\ResourceBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\Generator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

final class PimcoreResourceClassGenerator extends Generator
{
    public function generateResourceClass(BundleInterface $bundle, $modelName, $inheritFrom)
    {
        $dir = $bundle->getPath();
        $modelFile = $dir . '/Model/' . $modelName . '.php';

        if (file_exists($modelFile)) {
            throw new \RuntimeException(sprintf('Model "%s" already exists', $modelName));
        }

        $parameters = array(
            'namespace' => $bundle->getNamespace(),
            'bundle' => $bundle->getName(),
            'model' => $modelName,
            'inheritFrom' => $inheritFrom,
        );

        $this->renderFile('model/Resource.php.twig', $modelFile, $parameters);
    }
}
