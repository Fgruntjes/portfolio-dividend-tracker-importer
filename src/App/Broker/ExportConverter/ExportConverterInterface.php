<?php
declare(strict_types = 1);

namespace App\Broker\ExportConverter;

use App\Broker\BrokerExport;
use SplFileInfo;

interface ExportConverterInterface
{
    public function getName(): string;
    public function convertExport(SplFileInfo $brokerFile): BrokerExport;
}
