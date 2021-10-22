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

declare(strict_types=1);

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
