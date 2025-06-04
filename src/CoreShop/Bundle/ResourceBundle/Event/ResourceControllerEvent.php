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

namespace CoreShop\Bundle\ResourceBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

class ResourceControllerEvent extends GenericEvent
{
    public const TYPE_ERROR = 'error';

    public const TYPE_WARNING = 'warning';

    public const TYPE_INFO = 'info';

    public const TYPE_SUCCESS = 'success';

    private string $messageType = '';

    private string $message = '';

    private array $messageParameters = [];

    private int $errorCode = 500;

    private ?Response $response = null;

    public function stop(string $message, string $type = self::TYPE_ERROR, array $parameters = [], int $errorCode = 500): void
    {
        $this->messageType = $type;
        $this->message = $message;
        $this->messageParameters = $parameters;
        $this->errorCode = $errorCode;

        $this->stopPropagation();
    }

    public function isStopped(): bool
    {
        return $this->isPropagationStopped();
    }

    public function getMessageType(): string
    {
        return $this->messageType;
    }

    public function setMessageType($messageType): void
    {
        $this->messageType = $messageType;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessageParameters(): array
    {
        return $this->messageParameters;
    }

    public function setMessageParameters(array $messageParameters): void
    {
        $this->messageParameters = $messageParameters;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function setErrorCode(int $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }

    public function hasResponse(): bool
    {
        return null !== $this->response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
