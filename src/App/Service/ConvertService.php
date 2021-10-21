<?php
declare(strict_types = 1);

namespace App\Service;

use App\Broker\BrokerExportConverterRegistry;
use SplFileInfo;
use SplFileObject;
use Symfony\Component\HttpFoundation\File\Stream;

class ConvertService
{
    private BrokerExportConverterRegistry $brokerRegistry;

    public function __construct(BrokerExportConverterRegistry $brokerRegistry)
    {
        $this->brokerRegistry = $brokerRegistry;
    }

    public function convert(SplFileInfo $file, string $broker): Stream
    {

    }
}
