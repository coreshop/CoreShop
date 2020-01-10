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

namespace CoreShop\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use CoreShop\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class PimcoreContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var string
     */
    private $webRoot;

    /**
     * @var array
     */
    private $adminJs;

    /**
     * @var array
     */
    private $adminCss;

    /**
     * @var array
     */
    private $editmodeJs;

    /**
     * @var array
     */
    private $editmodeCss;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param string                 $webRoot
     * @param array                  $adminJs
     * @param array                  $adminCss
     * @param array                  $editmodeJs
     * @param array                  $editmodeCss
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        string $webRoot,
        array $adminJs,
        array $adminCss,
        array $editmodeJs,
        array $editmodeCss
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->webRoot = $webRoot;
        $this->adminJs = $adminJs;
        $this->adminCss = $adminCss;
        $this->editmodeJs = $editmodeJs;
        $this->editmodeCss = $editmodeCss;
    }

    /**
     * @Then /^all admin js resources should exist$/
     */
    public function allAdminJsResourceShouldExist()
    {
        $this->checkFilesExist($this->adminJs, 'Admin JS');
    }

    /**
     * @Then /^all admin css resources should exist$/
     */
    public function allAdminCssResourceShouldExist()
    {
        $this->checkFilesExist($this->adminCss, 'Admin CSS');
    }

    /**
     * @Then /^all editmode js resources should exist$/
     */
    public function allEditmodeJsResourceShouldExist()
    {
        $this->checkFilesExist($this->editmodeJs, 'Editmode JS');
    }

    /**
     * @Then /^all editmode css resources should exist$/
     */
    public function allEditmodeCssResourceShouldExist()
    {
        $this->checkFilesExist($this->editmodeCss, 'Editmode CSS');
    }

    /**
     * @param array  $files
     * @param string $type
     */
    private function checkFilesExist(array $files, string $type)
    {
        foreach ($files as $file) {
            Assert::true($this->checkFileExists($file), sprintf('File "%s" for type %s not found', $file, $type));
        }
    }

    /**
     * @param string $file
     *
     * @return bool
     */
    private function checkFileExists($file)
    {
        return file_exists(sprintf('%s%s', $this->webRoot, $file));
    }
}
