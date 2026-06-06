<?php

namespace App\Infrastructure\Providers;

use Illuminate\Database\Connectors\SqlServerConnector;
use Illuminate\Support\ServiceProvider;
use PDO;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            'db.connector.sqlsrv',
            function () {
                return new class extends SqlServerConnector
                {
                    protected $options = [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    ];
                };
            }
        );
    }
}
