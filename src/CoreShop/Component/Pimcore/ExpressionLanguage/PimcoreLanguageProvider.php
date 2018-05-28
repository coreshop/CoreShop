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

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class PimcoreLanguageProvider implements ExpressionFunctionProviderInterface
{
    private $serviceCompiler;

    public function __construct(callable $serviceCompiler = null)
    {
        $this->serviceCompiler = $serviceCompiler;
    }

    public function getFunctions()
    {
        return array(
            new ExpressionFunction('object', $this->serviceCompiler ?: function ($arg) {
                return sprintf('\\Pimcore\\Model\\DataObject::getById(%s)', $arg);
            }, function (array $variables, $value) {
                return DataObject::getById($value);
            }),

            new ExpressionFunction('asset', function ($arg) {
                return sprintf('\\Pimcore\\Model\\Asset::getById(%s)', $arg);
            }, function (array $variables, $value) {
                return Asset::getById($value);
            }),

            new ExpressionFunction('document', function ($arg) {
                return sprintf('\\Pimcore\\Model\\Document::getById(%s)', $arg);
            }, function (array $variables, $value) {
                return Document::getById($value);
            }),
        );
    }
}
