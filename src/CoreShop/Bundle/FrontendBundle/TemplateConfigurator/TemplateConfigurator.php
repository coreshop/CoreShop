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

namespace CoreShop\Bundle\FrontendBundle\TemplateConfigurator;

class TemplateConfigurator implements TemplateConfiguratorInterface
{
    /**
     * @var string
     */
    private $bundleName;

    /**
     * @var string
     */
    private $templateSuffix;

    /**
     * @param string $bundleName
     * @param string $templateSuffix
     */
    public function __construct(string $bundleName, string $templateSuffix)
    {
        $this->bundleName = $bundleName;
        $this->templateSuffix = $templateSuffix;
    }

    /**
     * {@inheritdoc}
     */
    public function findTemplate($templateName)
    {
        return sprintf('@%s/%s.%s', $this->bundleName, $templateName, $this->templateSuffix);
    }
}
