<?php
declare(strict_types = 1);

namespace App\Exception;

use Exception;

class BrokerAlreadyExistsException extends Exception
{
    private string $broker;

    public function __construct(
        string $broker,
        string $message = 'Broker with name `%s` is already known and can not be registered twice',
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
