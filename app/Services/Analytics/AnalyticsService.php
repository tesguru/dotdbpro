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
    $params = ['keyword' => $keyword];

    // Position filtering
    if (($options['position'] ?? 'any') === 'beginning') {
        $whereConditions[] = "domain_name LIKE :keyword_pattern_start";
        $params['keyword_pattern_start'] = $keyword . '%';
    } elseif (($options['position'] ?? 'any') === 'end') {
        $whereConditions[] = "domain_name LIKE :keyword_pattern_end";
        $params['keyword_pattern_end'] = '%' . $keyword;
    } else {
        $whereConditions[] = "domain_name LIKE :keyword_pattern_any";
        $params['keyword_pattern_any'] = '%' . $keyword . '%';
    }

    // Character type filtering (includes)
    $includes = $options['includes'] ?? [];
    if (!empty($includes)) {
        $charConditions = [];

        if (!($includes['alphabets'] ?? true)) {
            $charConditions[] = "domain_name NOT REGEXP '[a-zA-Z]'";
        }
        if (!($includes['digits'] ?? true)) {
            $charConditions[] = "domain_name NOT REGEXP '[0-9]'";
        }
        if (!($includes['hyphens'] ?? true)) {
            $charConditions[] = "domain_name NOT LIKE '%-%'";
        }
        if (!($includes['idns'] ?? true)) {
            // IDN filtering - domains with non-ASCII characters
            $charConditions[] = "domain_name REGEXP '^[a-zA-Z0-9.-]+$'";
        }

        if (!empty($charConditions)) {
            $whereConditions[] = "(" . implode(" AND ", $charConditions) . ")";
        }
    }

    // Length filtering
    if (!empty($options['minLength'] ?? '')) {
        $whereConditions[] = "length(domain_name) >= :min_length";
        $params['min_length'] = (int)$options['minLength'];
    }

    if (!empty($options['maxLength'] ?? '')) {
        $whereConditions[] = "length(domain_name) <= :max_length";
        $params['max_length'] = (int)$options['maxLength'];
    }

    // Exclude filtering
    if (!empty($options['exclude'] ?? '')) {
        $excludeTerms = array_map('trim', explode(',', $options['exclude']));
        foreach ($excludeTerms as $index => $term) {
            if (!empty($term)) {
                $whereConditions[] = "domain_name NOT LIKE :exclude_term_{$index}";
                $params["exclude_term_{$index}"] = "%{$term}%";
            }
        }
    }

    $whereClause = !empty($whereConditions) ? implode(' AND ', $whereConditions) : "1=1";
    $limit = $options['limit'] ?? 100;

    $query = "
        WITH latest_domains AS (
            SELECT
                domain_name,
                argMax(status, created_at) as latest_status
            FROM domains
            WHERE {$whereClause}
            GROUP BY domain_name
        ),
        domain_stats AS (
            SELECT
                arrayElement(splitByChar('.', domain_name), 1) as base_keyword,
                concat('.', arrayElement(splitByChar('.', domain_name), 2)) as extension,
                latest_status as extension_status
            FROM latest_domains
        )
        SELECT
            base_keyword as keyword,
            COUNT(*) as count,
            SUM(CASE WHEN extension_status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN extension_status = 'parked' THEN 1 ELSE 0 END) as parked,
            SUM(CASE WHEN extension_status = 'inactive' THEN 1 ELSE 0 END) as inactive,
            groupUniqArray(extension) as all_extensions,
            groupUniqArrayIf(extension, extension_status = 'active') as active_extensions,
            groupUniqArrayIf(extension, extension_status = 'parked') as parked_extensions,
            groupUniqArrayIf(extension, extension_status = 'inactive') as inactive_extensions,
            CASE
                WHEN active >= parked AND active >= inactive THEN 'active'
                WHEN parked >= active AND parked >= inactive THEN 'parked'
                ELSE 'inactive'
            END as status
        FROM domain_stats
        GROUP BY base_keyword
        ORDER BY count DESC
        LIMIT {$limit}
    ";

    $results = $this->client->select($query, $params)->rows();

    // Apply TLD filtering in PHP (since it's easier than complex SQL)
    return $this->filterResultsByExtensions($results, $options['extensions'] ?? []);
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


