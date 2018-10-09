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

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class PHPFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return array(
            ExpressionFunction::fromPHP('sprintf'),
            ExpressionFunction::fromPHP('substr'),
            ExpressionFunction::fromPHP('strlen'),
            ExpressionFunction::fromPHP('str_replace'),
            ExpressionFunction::fromPHP('strtolower'),
            ExpressionFunction::fromPHP('strtoupper'),
            ExpressionFunction::fromPHP('trim'),
            ExpressionFunction::fromPHP('ltrim'),
            ExpressionFunction::fromPHP('rtrim'),
            ExpressionFunction::fromPHP('ucfirst'),
            ExpressionFunction::fromPHP('lcfirst'),
            ExpressionFunction::fromPHP('ucwords'),
            ExpressionFunction::fromPHP('wordwrap'),
            ExpressionFunction::fromPHP('nl2br'),
            ExpressionFunction::fromPHP('number_format'),
            ExpressionFunction::fromPHP('strip_tags'),
            ExpressionFunction::fromPHP('strrev'),
            ExpressionFunction::fromPHP('intval'),
            ExpressionFunction::fromPHP('doubleval'),
            ExpressionFunction::fromPHP('floatval'),
            ExpressionFunction::fromPHP('round'),
            ExpressionFunction::fromPHP('explode'),
            ExpressionFunction::fromPHP('implode'),
            ExpressionFunction::fromPHP('is_array'),
        );
    }
}
