<?php

namespace Tests\Support;

use Illuminate\Support\Facades\DB;

trait PostgresTemporario
{
    protected function iniciarPostgresTemporario(): void
    {
        if (getenv('SIAPRA_TEST_PGSQL') !== '1') {
            $this->markTestSkipped('Requer PostgreSQL descartável e SIAPRA_TEST_PGSQL=1; não usa o .env.');
        }
        // Conexão explícita, independente das credenciais e URL do ambiente real.
        config(['database.default' => 'pendencias_test', 'database.connections.pendencias_test' => [
            'driver' => 'pgsql', 'host' => '127.0.0.1',
            'port' => getenv('SIAPRA_TEST_PGPORT') ?: '55438',
            'database' => 'postgres', 'username' => 'siapra_test', 'password' => '',
            'charset' => 'utf8', 'prefix' => '', 'search_path' => 'pg_temp', 'sslmode' => 'disable',
        ]]);
        DB::purge('pendencias_test');
        DB::beginTransaction();
        DB::statement('SET LOCAL search_path TO pg_temp');
    }

    protected function encerrarPostgresTemporario(): void
    {
        if (config('database.default') === 'pendencias_test') {
            while (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            DB::disconnect('pendencias_test');
        }
    }
}
