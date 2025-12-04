<?php

namespace App\Services\Analytics;

use ClickHouseDB\Client;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
class AnalyticsService
{
    protected Client $client;

    protected $baseUrl;
    protected $model;
    public function __construct()
    {
        $this->client = new Client([
            'host' => '127.0.0.1',
            'port' => '8123',
            'username' => 'default',
            'password' => ''
        ]);

         $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama3.2:1b');

        // Set default database
        $this->client->database('domains_db');
    }

    /**
     * Get limited rows from a ClickHouse table
     *
     * @param string $table Table name
     * @param int $limit Number of rows to fetch
     * @return array
     */
    public function getDataLimit(string $table = 'domains', int $limit = 600): array
    {
        try {
            // Sanitize table name to prevent injection (basic check)
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) {
                throw new Exception("Invalid table name: $table");
            }

            $query = "SELECT * FROM {$table} LIMIT {$limit}";

            $result = $this->client->select($query);

            return $result->rows();

        } catch (Exception $e) {
            // Log error or handle it gracefully
            // \Log::error('ClickHouse query failed: '.$e->getMessage());
            return [];
        }
    }

// public function getRelatedDomains(string $keyword, int $limit = 100): array
// {
//     try {
//         $limit = (int) $limit;

//         $query = "
//             SELECT domain, status
//             FROM domains
//             WHERE lower(domain) LIKE lower(:keyword)
//             LIMIT {$limit}
//         ";

//         $result = $this->client->select($query, [
//             'keyword' => "%{$keyword}%"
//         ]);

//         $domains = $result->rows();

//         // Extract only the domain names
//         $matchedDomains = array_map(fn($row) => $row['domain'], $domains);

//         return $matchedDomains;

//     } catch (\Exception $e) {
//         return [];
//     }
// }

public function getRelatedDomains(string $keyword, array $options = []): array
{
    $whereConditions = [];
    $bindings = [];

    // Position filtering
    if (($options['position'] ?? 'any') === 'beginning') {
        $whereConditions[] = "domain LIKE ?";
        $bindings[] = $keyword . '%';
    } elseif (($options['position'] ?? 'any') === 'end') {
        $whereConditions[] = "domain LIKE ?";
        $bindings[] = '%' . $keyword;
    } else {
        $whereConditions[] = "domain LIKE ?";
        $bindings[] = '%' . $keyword . '%';
    }

    // Character type filtering (includes)
    $includes = $options['includes'] ?? [];
    if (!empty($includes)) {
        $charConditions = [];

        if (!($includes['alphabets'] ?? true)) {
            $charConditions[] = "domain NOT REGEXP '[a-zA-Z]'";
        }
        if (!($includes['digits'] ?? true)) {
            $charConditions[] = "domain NOT REGEXP '[0-9]'";
        }
        if (!($includes['hyphens'] ?? true)) {
            $charConditions[] = "domain NOT LIKE '%-%'";
        }
        if (!($includes['idns'] ?? true)) {
            // IDN filtering - domains with non-ASCII characters
            $charConditions[] = "domain REGEXP '^[a-zA-Z0-9.-]+$'";
        }

        if (!empty($charConditions)) {
            $whereConditions[] = "(" . implode(" AND ", $charConditions) . ")";
        }
    }

    // Length filtering
    if (!empty($options['minLength'] ?? '')) {
        $whereConditions[] = "length(domain) >= ?";
        $bindings[] = (int)$options['minLength'];
    }

    if (!empty($options['maxLength'] ?? '')) {
        $whereConditions[] = "length(domain) <= ?";
        $bindings[] = (int)$options['maxLength'];
    }

    // Exclude filtering
    if (!empty($options['exclude'] ?? '')) {
        $excludeTerms = array_map('trim', explode(',', $options['exclude']));
        foreach ($excludeTerms as $index => $term) {
            if (!empty($term)) {
                $whereConditions[] = "domain NOT LIKE ?";
                $bindings[] = "%{$term}%";
            }
        }
    }

    $whereClause = !empty($whereConditions) ? implode(' AND ', $whereConditions) : "1=1";
    $limit = $options['limit'] ?? 100;

    $query = "
        WITH unique_domains AS (
            SELECT
                domain,
                MAX(created_at) as latest_created_at
            FROM domains
            WHERE {$whereClause}
            GROUP BY domain
        ),
        domain_stats AS (
            SELECT
                arrayElement(splitByChar('.', domain), 1) as base_keyword,
                concat('.', arrayElement(splitByChar('.', domain), 2)) as extension
            FROM unique_domains
        )
        SELECT
            base_keyword as keyword,
            COUNT(*) as count,
            groupUniqArray(extension) as all_extensions
        FROM domain_stats
        GROUP BY base_keyword
        ORDER BY count DESC
        LIMIT {$limit}
    ";

    return $this->client->select($query, $bindings)->rows();
}

private function filterResultsByExtensions(array $results, array $selectedExtensions): array
{
    if (empty($selectedExtensions)) {
        return $results;
    }

    return array_filter($results, function($result) use ($selectedExtensions) {
        // Check if any of the result's extensions match the selected ones
        $matchingExtensions = array_intersect($result['all_extensions'], $selectedExtensions);
        return !empty($matchingExtensions);
    });
}


 public function analyzeDomain(string $domain): array
    {
           $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama3.2:1b');
        // Check cache first (1 hour)
        $cacheKey = "domain_analysis_{$domain}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $startTime = microtime(true);

        $prompt = "Domain: {$domain}. What are 3 business ideas? 40 words max.";

        try {
            $response = Http::timeout(40)
                ->post("{$this->baseUrl}/api/generate", [
                    'model' => $this->model,
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => 0.7,
                        'num_predict' => 80,
                    ]
                ]);

            if ($response->successful()) {
                $result = [
                    'success' => true,
                    'domain' => $domain,
                    'analysis' => $response->json('response'),
                    'time' => round(microtime(true) - $startTime, 2),
                ];

                // Cache for 1 hour
                Cache::put($cacheKey, $result, 3600);

                return $result;
            }

            return [
                'success' => false,
                'error' => 'AI service unavailable',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }





}


