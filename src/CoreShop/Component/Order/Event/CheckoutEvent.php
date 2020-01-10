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

namespace CoreShop\Component\Order\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

class CheckoutEvent extends GenericEvent
{
    public const TYPE_ERROR = 'error';
    public const TYPE_WARNING = 'warning';
    public const TYPE_INFO = 'info';
    public const TYPE_SUCCESS = 'success';

    /**
     * @var string
     */
    private $messageType = '';

    /**
     * @var string
     */
    private $message = '';

    /**
     * @var array
     */
    private $messageParameters = [];

    /**
     * @var int
     */
    private $errorCode = 500;

    /**
     * @var Response
     */
    private $response;

    public function stop(string $message, string $type = self::TYPE_ERROR, array $parameters = [], int $errorCode = 500)
    {
        $this->messageType = $type;
        $this->message = $message;
        $this->messageParameters = $parameters;
        $this->errorCode = $errorCode;
        $this->stopPropagation();
    }

    public function isStopped()
    {
        return $this->isPropagationStopped();
    }

    public function getMessageType()
    {
        return $this->messageType;
    }

    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function getMessageParameters()
    {
        return $this->messageParameters;
    }

    public function setMessageParameters(array $messageParameters)
    {
        $this->messageParameters = $messageParameters;
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }

    public function setErrorCode(int $errorCode)
    {
        $this->errorCode = $errorCode;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function hasResponse()
    {
        return null !== $this->response;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
