<?php
declare(strict_types = 1);

namespace App\Broker\ExportConverter;

use App\Exception\BrokerDoesNotExistException;
use App\Exception\BrokerAlreadyExistsException;

class ExportConverterRegistry
{
    /**
     * @var ExportConverterInterface[]
     */
    private array $brokers = [];

    /**
     * @throws BrokerAlreadyExistsException
     */
    public function add(ExportConverterInterface $broker): void
    {
        $type = get_class($broker);
        if ($this->has($type)) {
            throw new BrokerAlreadyExistsException($type);
        }
        $this->brokers[$type] = $broker;
    }

    /**
     * @param string $type
     * @return ExportConverterInterface
     * @throws BrokerDoesNotExistException
     */
    public function get(string $type): ExportConverterInterface
    {
        if (!$this->has($type)) {
            throw new BrokerDoesNotExistException($type);
        }
        return $this->brokers[$type];
    }

    public function has(string $type): bool
    {
        return array_key_exists($type, $this->brokers);
    }

    /**
     * @return string[]
     */
    public function getChoices(): array
    {
        $result = [];
        /** @var ExportConverterInterface $broker */
        foreach ($this->brokers as $type => $broker) {
            $result[$broker->getName()] = $type;
        }
        return $result;
    }
}
