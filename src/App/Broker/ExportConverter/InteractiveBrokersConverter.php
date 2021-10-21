<?php
declare(strict_types = 1);

namespace App\Broker\ExportConverter;

use App\Broker\BrokerExport;
use SplFileInfo;

class InteractiveBrokersConverter implements ExportConverterInterface
{
    public const NAME = 'Interactive Brokers';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertExport(SplFileInfo $brokerFile): BrokerExport
    {
        // TODO: Implement convertExport() method.
    }
}
