<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Behat\Page\Pimcore;

abstract class AbstractCoreShopResourcePage extends AbstractPimcoreTabPage
{
    public function create(string $name): void
    {
        $addButton = $this->extjsComponentQuery('[itemId=add-button]');
        $addButton->click();

        $newDialog = $this->extsDocumentQuery('[itemId='.$this->getLayoutId().'-new-dialog]');
        $newDialog->find('css', 'input')->setValue($name);

        $okButton = $this->extjsComponentQuery('[itemId=ok]', $newDialog->getAttribute('id'));
        $okButton->click();
    }
}
