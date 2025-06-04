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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Pimcore\ExpressionLanguage;

use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class PHPFunctionsProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
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
            ExpressionFunction::fromPHP('count'),
            ExpressionFunction::fromPHP('dirname'),
            ExpressionFunction::fromPHP('basename'),
            ExpressionFunction::fromPHP('array_unique'),
            ExpressionFunction::fromPHP('array_filter'),
        ];
    }
}
