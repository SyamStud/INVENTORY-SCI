<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateDatabase extends Command
{
    protected $signature = 'db:create';
    protected $description = 'Create a new database and import required data';

    public function handle()
    {
        try {
            $database = Config::get('database.connections.mysql.database');
            $config = Config::get('database.connections.mysql');

            $this->info("Creating database...");

            // Temporarily remove database name from config
            $config['database'] = null;
            Config::set('database.connections.mysql', $config);
            DB::purge('mysql');

            // Create the database
            DB::statement("CREATE DATABASE IF NOT EXISTS `" . str_replace("`", "``", $database) . "`");

            // Reset the database name in the configuration
            $config['database'] = $database;
            Config::set('database.connections.mysql', $config);
            DB::purge('mysql');
            DB::reconnect('mysql');

            $this->info("Database created successfully. Importing Indonesia regions data...");

            // Import SQL file for Indonesia regions
            $sqlFile = database_path('indonesia.sql');
            if (file_exists($sqlFile)) {
                // Read file in chunks to prevent memory issues
                $sql = file_get_contents($sqlFile);

                // Split the SQL file into individual queries
                $queries = array_filter(
                    array_map(
                        'trim',
                        explode(';', $sql)
                    )
                );

                // Execute each query separately
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        try {
                            DB::unprepared($query . ';');
                        } catch (\Exception $queryException) {
                            $this->warn("Warning: Failed to execute query: " . $queryException->getMessage());
                            continue;
                        }
                    }
                }

                $this->info("Indonesia regions data imported successfully.");
            } else {
                $this->warn("Indonesia regions SQL file not found at: " . $sqlFile);
            }

            // Force refresh the database connection
            DB::purge('mysql');
            DB::reconnect('mysql');

            $this->info("Running migrations...");

            // Run migrations with force flag
            $this->call('migrate', [
                '--force' => true
            ]);

            $this->info("Database setup completed successfully!");
        } catch (\Exception $e) {
            $this->error("Failed to setup database '$database': " . $e->getMessage());
            $this->error("Error trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
