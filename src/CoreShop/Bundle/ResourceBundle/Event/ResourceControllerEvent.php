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

namespace CoreShop\Bundle\ResourceBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

class ResourceControllerEvent extends GenericEvent
{
    const TYPE_ERROR = 'error';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';
    const TYPE_SUCCESS = 'success';

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

    /**
     * @param string $message
     * @param string $type
     * @param array  $parameters
     * @param int    $errorCode
     */
    public function stop($message, $type = self::TYPE_ERROR, $parameters = [], $errorCode = 500)
    {
        $this->messageType = $type;
        $this->message = $message;
        $this->messageParameters = $parameters;
        $this->errorCode = $errorCode;

        $this->stopPropagation();
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return $this->isPropagationStopped();
    }

    /**
     * @return string
     */
    public function getMessageType()
    {
        return $this->messageType;
    }

    /**
     * @param string $messageType Should be one of ResourceEvent's TYPE constants
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return array
     */
    public function getMessageParameters()
    {
        return $this->messageParameters;
    }

    /**
     * @param array $messageParameters
     */
    public function setMessageParameters(array $messageParameters)
    {
        $this->messageParameters = $messageParameters;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
