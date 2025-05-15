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

use Pimcore\Model\Asset;
use Pimcore\Model\DataObject;
use Pimcore\Model\Document;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class PimcoreLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('object', function (int $arg) {
                return sprintf('\\Pimcore\\Model\\DataObject::getById(%s)', $arg);
            }, static function (array $variables, int $value) {
                return DataObject::getById($value);
            }),

            new ExpressionFunction('asset', function (int $arg) {
                return sprintf('\\Pimcore\\Model\\Asset::getById(%s)', $arg);
            }, static function (array $variables, int $value) {
                return Asset::getById($value);
            }),

            new ExpressionFunction('document', function (int $arg) {
                return sprintf('\\Pimcore\\Model\\Document::getById(%s)', $arg);
            }, static function (array $variables, int $value) {
                return Document::getById($value);
            }),
        ];
    }
}
