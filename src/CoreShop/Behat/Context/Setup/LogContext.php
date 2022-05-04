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

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Facebook\WebDriver\Exception\WebDriverException;

final class LogContext implements Context
{
    public function __construct(private Mink $mink, private string $logDirectory)
    {
    }

    /**
     * @Given /^screenshot$/
     */
    public function screenshot(): void
    {
        $session = $this->getSession();

        try {
            $this->saveLog($session->getScreenshot(), 'png');
        } catch (UnsupportedDriverActionException|WebDriverException $exception) {
            //Ignore
        }
    }

    private function getSession(): Session
    {
        return $this->mink->getSession();
    }

    private function saveLog(string $content, string $type): void
    {
        $path = sprintf("%s/behat-%s.%s", $this->logDirectory, date('YmdHis'), $type);

        if (file_put_contents($path, $content) === false) {
            throw new \RuntimeException(sprintf('Failed while trying to write log in "%s".', $path));
        }
    }
}
