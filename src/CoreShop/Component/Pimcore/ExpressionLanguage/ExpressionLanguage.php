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

namespace CoreShop\Component\Pimcore\ExpressionLanguage;

use Symfony\Component\DependencyInjection\ExpressionLanguage as BaseExpressionLanguage;

/**
 * @deprecated Class CoreShop\Component\Pimcore\ExpressionLanguage\ExpressionLanguage is deprecated since version 2.0.0-beta.4 and will be removed in 2.0. Use the @coreshop.expression_language or manually create the ExpressionLanguage with the Providers instead.
 */
class ExpressionLanguage extends BaseExpressionLanguage
{
    /**
     * {@inheritdoc}
     */
    public function __construct($cache = null, array $providers = array(), callable $serviceCompiler = null)
    {
        @trigger_error('Class CoreShop\Component\Pimcore\ExpressionLanguage\ExpressionLanguage is deprecated since version 2.0.0-beta.4 and will be removed in 2.0. Use the @coreshop.expression_language or manually create the ExpressionLanguage with the Providers instead.', E_USER_DEPRECATED);

        // prepend the default provider to let users override it easily
        array_unshift($providers, new PimcoreLanguageProvider());
        array_unshift($providers, new PHPFunctionsProvider());

        parent::__construct($cache, $providers);
    }
}
