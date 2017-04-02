<?php

namespace CoreShop\Component\Resource\Exception;

class UpdateHandlingException extends \Exception
{
    /**
     * @var string
     */
    protected $flash;

    /**
     * @var int
     */
    protected $apiResponseCode;

    /**
     * @param string $message
     * @param string $flash
     * @param int $apiResponseCode
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct(
        $message = 'Ups, something went wrong, please try again.',
        $flash = 'something_went_wrong_error',
        $apiResponseCode = 400,
        $code = 0,
        \Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->flash = $flash;
        $this->apiResponseCode = $apiResponseCode;
    }

    /**
     * @return string
     */
    public function getFlash()
    {
        return $this->flash;
    }

    /**
     * @return int
     */
    public function getApiResponseCode()
    {
        return $this->apiResponseCode;
    }
}
