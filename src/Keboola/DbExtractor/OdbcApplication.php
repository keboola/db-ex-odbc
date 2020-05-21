<?php

declare(strict_types=1);

namespace Keboola\DbExtractor;

use Keboola\DbExtractor\Configuration\OdbcConfigurationRowDefinition;
use Keboola\DbExtractor\Configuration\OdbcConfigurationDefinition;

class OdbcApplication extends Application
{
    public function __construct(array $config, ?Logger $logger = null, array $state = [], string $dataDir = '/data/')
    {
        $config['parameters']['data_dir'] = $dataDir;
        $config['parameters']['extractor_class'] = 'ODBC';

        parent::__construct($config, ($logger) ? $logger : new Logger("ex-db-odbc"), $state);

        // override with mssql specific config definitions
        if (isset($this['parameters']['tables'])) {
            $this->setConfigDefinition(new OdbcConfigurationDefinition());
        } else if ($this['action'] === 'run') {
            $this->setConfigDefinition(new OdbcConfigurationRowDefinition());
        }
    }
}
