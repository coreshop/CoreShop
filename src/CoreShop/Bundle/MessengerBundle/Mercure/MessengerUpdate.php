<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Bundle\MessengerBundle\Mercure;

final readonly class MessengerUpdate implements \JsonSerializable
{
    public const TYPE_MESSAGE_HANDLED = 'message_handled';
    public const TYPE_MESSAGE_FAILED = 'message_failed';
    public const TYPE_MESSAGE_RETRIED = 'message_retried';
    public const TYPE_MESSAGE_REJECTED = 'message_rejected';

    public function __construct(
        private string $type,
        private string $receiverName,
        private string $messageClass,
        private ?string $messageId = null,
        private ?string $errorMessage = null,
        private ?int $relatedObjectId = null,
    ) {
    }

    public static function handled(
        string $receiverName,
        string $messageClass,
        ?string $messageId = null,
        ?int $relatedObjectId = null,
    ): self {
        return new self(
            self::TYPE_MESSAGE_HANDLED,
            $receiverName,
            $messageClass,
            $messageId,
            null,
            $relatedObjectId,
        );
    }

    public static function failed(
        string $receiverName,
        string $messageClass,
        ?string $messageId = null,
        ?string $errorMessage = null,
        ?int $relatedObjectId = null,
    ): self {
        return new self(
            self::TYPE_MESSAGE_FAILED,
            $receiverName,
            $messageClass,
            $messageId,
            $errorMessage,
            $relatedObjectId,
        );
    }

    public static function retried(
        string $receiverName,
        string $messageClass,
        ?string $messageId = null,
    ): self {
        return new self(
            self::TYPE_MESSAGE_RETRIED,
            $receiverName,
            $messageClass,
            $messageId,
        );
    }

    public static function rejected(
        string $receiverName,
        string $messageClass,
        ?string $messageId = null,
    ): self {
        return new self(
            self::TYPE_MESSAGE_REJECTED,
            $receiverName,
            $messageClass,
            $messageId,
        );
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getReceiverName(): string
    {
        return $this->receiverName;
    }

    public function getMessageClass(): string
    {
        return $this->messageClass;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getRelatedObjectId(): ?int
    {
        return $this->relatedObjectId;
    }

    public function jsonSerialize(): array
    {
        return [
            'eventType' => 'coreshop.messenger.update',
            'type' => $this->type,
            'receiverName' => $this->receiverName,
            'messageClass' => $this->messageClass,
            'messageId' => $this->messageId,
            'errorMessage' => $this->errorMessage,
            'relatedObjectId' => $this->relatedObjectId,
            'timestamp' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];
    }
}
