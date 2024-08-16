<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the database if it does not exist';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $databaseName = $this->argument('name') ?: Config::get('database.connections.pgsql.database');

        Config::set('database.connections.pgsql.database', null);

        $query = "SELECT datname FROM pg_catalog.pg_database WHERE datname = '{$databaseName}';";
        $result = DB::connection('pgsql')->select($query);

        if (empty($result)) {
            DB::statement("CREATE DATABASE \"{$databaseName}\"");
            $this->info("Database '{$databaseName}' created successfully.");
        } else {
            $this->info("Database '{$databaseName}' already exists.");
        }

        Config::set('database.connections.pgsql.database', $databaseName);

        return 0;
    }
}
