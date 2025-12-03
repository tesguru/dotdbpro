<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ClickHouseDB\Client;

class StreamDomains extends Command
{
    protected $signature = 'domains:stream {file}';
    protected $description = 'Stream domains from file into ClickHouse';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $clickhouse = new Client([
            'host' => '127.0.0.1',
            'port' => '8123',
            'username' => 'default',
            'password' => ''
        ]);

        $clickhouse->database('domains_db');

        // Handle gzipped files
        if (str_ends_with($file, '.gz')) {
            $handle = gzopen($file, 'r');
        } else {
            $handle = fopen($file, 'r');
        }

        if (!$handle) {
            $this->error("Could not open file: {$file}");
            return 1;
        }

        $batch = [];
        $batchSize = 10000;
        $total = 0;
        $skipped = 0;

        $this->info("Starting to stream domains from {$file}...");

        while (!feof($handle)) {
            $line = str_ends_with($file, '.gz') ? gzgets($handle) : fgets($handle);

            if ($line === false) break;

            $line = trim($line);

            if (empty($line)) continue;

            // Extract just the domain name
            $parts = preg_split('/\s+/', $line, 2);
            $domain = $parts[0] ?? '';
            $domain = rtrim($domain, '.');

            // Only insert if it looks like a valid domain
            if (!empty($domain) && str_contains($domain, '.')) {
                // Random status for simulation (70% active, 30% inactive)
                $status = (rand(1, 100) <= 70) ? 'active' : 'inactive';

                $batch[] = [
                    $domain,
                    date('Y-m-d H:i:s'),
                    $status
                ];

                if (count($batch) >= $batchSize) {
                    $clickhouse->insert('domains', $batch, ['domain_name', 'created_at', 'status']);
                    $total += count($batch);
                    $this->info("Inserted {$total} domains... (skipped {$skipped} invalid lines)");
                    $batch = [];
                }
            } else {
                $skipped++;
            }
        }

        // Insert remaining batch
        if (!empty($batch)) {
            $clickhouse->insert('domains', $batch, ['domain_name', 'created_at', 'status']);
            $total += count($batch);
        }

        if (str_ends_with($file, '.gz')) {
            gzclose($handle);
        } else {
            fclose($handle);
        }

        $this->info("✅ Done! Total domains inserted: {$total}");
        $this->info("Skipped {$skipped} invalid/duplicate lines");

        return 0;
    }
}
