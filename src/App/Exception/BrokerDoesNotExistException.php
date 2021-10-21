<?php
declare(strict_types = 1);

namespace App\Exception;

use Exception;
use Throwable;

class BrokerDoesNotExistException extends Exception
{
    private string $broker;

    public function __construct(
        string $broker,
        string $message = 'Broker with name `%s` does not exist',
        int $code = 0,
        Throwable $previous = null,
    ) {
        parent::__construct(sprintf($message, $broker), $code, $previous);
        $this->broker = $broker;
    }

    public function getBroker(): string
    {
        return $this->broker;
    }
}
