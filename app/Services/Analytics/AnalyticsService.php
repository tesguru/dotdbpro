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
//             SELECT domain_name, status
//             FROM domains
//             WHERE lower(domain_name) LIKE lower(:keyword)
//             LIMIT {$limit}
//         ";

//         $result = $this->client->select($query, [
//             'keyword' => "%{$keyword}%"
//         ]);

//         $domains = $result->rows();

//         // Extract only the domain names
//         $matchedDomains = array_map(fn($row) => $row['domain_name'], $domains);

//         return $matchedDomains;

//     } catch (\Exception $e) {
//         return [];
//     }
// }

public function getRelatedDomains(string $keyword, array $options = []): array
{
    $whereConditions = [];
    $params = [];

    // Build WHERE conditions with named parameters
    $position = $options['position'] ?? 'any';
    if ($position === 'beginning') {
        $whereConditions[] = "domain LIKE :keyword";
        $params['keyword'] = $keyword . '%';
    } elseif ($position === 'end') {
        $whereConditions[] = "domain LIKE :keyword";
        $params['keyword'] = '%' . $keyword;
    } else {
        $whereConditions[] = "domain LIKE :keyword";
        $params['keyword'] = '%' . $keyword . '%';
    }

    // Add other conditions without parameters
    $includes = $options['includes'] ?? [];
    if (!empty($includes)) {
        $charConditions = [];
        if (!($includes['alphabets'] ?? true)) {
            $charConditions[] = "NOT match(domain, '[a-zA-Z]')";
        }
        if (!($includes['digits'] ?? true)) {
            $charConditions[] = "NOT match(domain, '[0-9]')";
        }
        if (!($includes['hyphens'] ?? true)) {
            $charConditions[] = "domain NOT LIKE '%-%'";
        }
        if (!($includes['idns'] ?? true)) {
            $charConditions[] = "match(domain, '^[a-zA-Z0-9.-]+$')";
        }
        if (!empty($charConditions)) {
            $whereConditions[] = "(" . implode(" AND ", $charConditions) . ")";
        }
    }

    // Check length of keyword part only (before the first dot)
    if (!empty($options['minLength'] ?? '')) {
        $whereConditions[] = "length(splitByChar('.', domain)[1]) >= :min_length";
        $params['min_length'] = (int)$options['minLength'];
    }

    if (!empty($options['maxLength'] ?? '')) {
        $whereConditions[] = "length(splitByChar('.', domain)[1]) <= :max_length";
        $params['max_length'] = (int)$options['maxLength'];
    }

    if (!empty($options['exclude'] ?? '')) {
        $excludeTerms = array_map('trim', explode(',', $options['exclude']));
        foreach ($excludeTerms as $index => $term) {
            if (!empty($term)) {
                $whereConditions[] = "domain NOT LIKE :exclude_{$index}";
                $params["exclude_{$index}"] = "%{$term}%";
            }
        }
    }

    $whereClause = !empty($whereConditions) ? implode(' AND ', $whereConditions) : "1=1";
    $limit = $options['limit'] ?? 100;

    $query = "
        SELECT
            splitByChar('.', domain)[1] as keyword,
            count() as count,
            groupUniqArray(
                '.' || splitByChar('.', domain)[2]
            ) as all_extensions
        FROM domains_db.domains
        WHERE {$whereClause}
        GROUP BY keyword
        ORDER BY count DESC
        LIMIT {$limit}
    ";

    $results = $this->client->select($query, $params)->rows();

    // Apply extension filtering if specified
    $selectedExtensions = $options['extensions'] ?? [];
    if (!empty($selectedExtensions)) {
        $results = $this->filterResultsByExtensions($results, $selectedExtensions);
    }

    return $results;
}

private function filterResultsByExtensions(array $results, array $selectedExtensions): array
{
    if (empty($selectedExtensions)) {
        return $results;
    }

   
    return array_values(array_filter($results, function($result) use ($selectedExtensions) {

        if (!isset($result['all_extensions']) || !is_array($result['all_extensions'])) {
            return false;
        }
        $resultExtensions = array_map('trim', $result['all_extensions']);
        $selectedExtensions = array_map('trim', $selectedExtensions);
        $matchingExtensions = array_intersect($resultExtensions, $selectedExtensions);

        return !empty($matchingExtensions);
    }));
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


