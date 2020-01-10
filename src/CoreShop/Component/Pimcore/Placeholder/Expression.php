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

namespace CoreShop\Component\Pimcore\Placeholder;

use Pimcore\Placeholder\AbstractPlaceholder;

class Expression extends AbstractPlaceholder
{
    /**
     * {@inheritdoc}
     */
    public function getTestValue()
    {
        return '<span class="testValue">Name of the Object</span>';
    }

    /**
     * {@inheritdoc}
     */
    public function getReplacement()
    {
        $expr = \Pimcore::getContainer()->get('coreshop.expression_language');
        $config = $this->getPlaceholderConfig();
        $expression = $config->get('expression');

        return $expr->evaluate($expression, [
            'value' => $this->getValue(),
            'key' => $this->getPlaceholderKey(),
            'config' => $this->getPlaceholderConfig()->toArray(),
            'container' => \Pimcore::getContainer(),
            'params' => $this->getParams(),
            'placeholder' => $this,
        ]);
    }
}
