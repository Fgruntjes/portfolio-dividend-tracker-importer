<?php
declare(strict_types = 1);

namespace App\Entity;

use App\Validator\BrokerExportConverter;

class ExportExportUpload
{
    private string $file;

    #[BrokerExportConverter()]
    private string $broker;

    public function getFile(): string
    {
        return $this->file;
    }

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function getBroker(): string
    {
        return $this->broker;
    }

    public function setBroker(string $broker): void
    {
        $this->broker = $broker;
    }
}
