<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 */

declare(strict_types=1);

namespace CoreShop\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\Exception as MinkException;
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
    /**
     * @Given /^html log/
     */
    public function htmlLog(): void
    {
        $session = $this->getSession();

        $log = sprintf('Current page: %d %s', $this->getStatusCode($session), $this->getCurrentUrl($session)) . "\n";
        $log .= $this->getResponseHeadersLogMessage($session);
        $log .= $this->getResponseContentLogMessage($session);

        $this->saveLog($log, 'html');
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
    private function getStatusCode(Session $session): ?int
    {
        try {
            return $session->getStatusCode();
        } catch (MinkException | WebDriverException $exception) {
            return null;
        }
    }

    private function getCurrentUrl(Session $session): ?string
    {
        try {
            return $session->getCurrentUrl();
        } catch (MinkException | WebDriverException $exception) {
            return null;
        }
    }

    private function getResponseHeadersLogMessage(Session $session): ?string
    {
        try {
            return 'Response headers:' . "\n" . print_r($session->getResponseHeaders(), true) . "\n";
        } catch (MinkException | WebDriverException $exception) {
            return null;
        }
    }

    private function getResponseContentLogMessage(Session $session): ?string
    {
        try {
            return 'Response content:' . "\n" . $session->getPage()->getContent() . "\n";
        } catch (MinkException | WebDriverException $exception) {
            return null;
        }
    }
}
