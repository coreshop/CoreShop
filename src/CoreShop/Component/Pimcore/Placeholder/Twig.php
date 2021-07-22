<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\Placeholder;

use Pimcore\Placeholder\AbstractPlaceholder;

class Twig extends AbstractPlaceholder
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
        $twig = \Pimcore::getContainer()->get('twig');
        $config = $this->getPlaceholderConfig();
        $template = $config->get('template');

        return $twig->render($template, [
            $this->getPlaceholderKey() => $this->getValue(),
            'value' => $this->getValue(),
            'key' => $this->getPlaceholderKey(),
            'config' => $this->getPlaceholderConfig()->toArray(),
            'params' => $this->getParams(),
            'placeholder' => $this,
        ]);
    }
}
