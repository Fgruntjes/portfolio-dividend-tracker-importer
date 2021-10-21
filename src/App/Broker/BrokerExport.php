<?php
declare(strict_types = 1);

namespace App\Broker;

use SplFileObject;

class BrokerExport
{
    private SplFileObject $file;

    public function __construct()
    {
        $this->file = new SplFileObject('php://temp', 'r+');
    }

    public function getStream(): SplFileObject
    {
        return $this->file;
    }
}
