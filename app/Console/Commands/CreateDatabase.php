<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {--migrate : Ejecuta las migraciones después de crear la base de datos} {--seed : Ejecuta los seeders después de las migraciones}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea la base de datos y opcionalmente ejecuta las migraciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $connectionName = config('database.default');
        $allConnections = config('database.connections');
        $config = $allConnections[$connectionName] ?? null;

        if (!$config) {
            $this->error("No se ha encontrado la configuración para la conexión '{$connectionName}' en config/database.php");
            return 1;
        }

        $driver = $config['driver'] ?? null;
        $database = $config['database'] ?? null;

        if (!$database) {
            $this->error("No se ha definido el nombre de la base de datos para la conexión '{$connectionName}'");
            return 1;
        }

        try {
            if ($driver === 'sqlite') {
                if (!file_exists($database)) {
                    $directory = dirname($database);
                    if (!file_exists($directory) && $directory !== '.') {
                        mkdir($directory, 0755, true);
                    }
                    touch($database);
                    $this->info("Base de datos SQLite creada en: {$database}");
                }
            } elseif ($driver === 'mysql') {
                // Construir DSN sin base de datos para conectar al servidor
                $dsn = "mysql:host={$config['host']};port={$config['port']}";

                $pdo = new \PDO($dsn, $config['username'], $config['password']);

                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$config['charset']} COLLATE {$config['collation']};");
                $this->info("Base de datos '{$database}' asegurada.");
            } else {
                $this->warn("El driver '{$driver}' no está soportado para creación automática.");
            }

            if ($this->option('migrate')) {
                // Purgar la conexión para asegurar que Laravel reconozca la nueva DB
                DB::purge($connectionName);
                
                $this->info("Ejecutando migraciones...");
                $params = ['--force' => true];
                if ($this->option('seed')) {
                    $params['--seed'] = true;
                }
                $this->call('migrate', $params);
            }

            return 0;

        } catch (\PDOException $e) {
            $this->error("Error de conexión/SQL: " . $e->getMessage());
            return 1;
        } catch (\Exception $e) {
            $this->error("Error general: " . $e->getMessage());
            return 1;
        }
    }
}
