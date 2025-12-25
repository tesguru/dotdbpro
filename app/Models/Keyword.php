<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Keyword extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'keyword'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'keywords';

    /**
     * Find or create a keyword
     *
     * @param string $keyword
     * @return \App\Models\Keyword
     */
    public static function findOrCreate(string $keyword): self
    {
        return static::firstOrCreate(
            ['keyword' => $keyword],
            ['keyword' => $keyword]
        );
    }

    /**
     * Search for keywords
     *
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function search(string $search)
    {
        return static::where('keyword', 'LIKE', "%{$search}%")
            ->orderBy('keyword')
            ->get();
    }

    /**
     * Get all keywords as a simple array
     *
     * @return array
     */
    public static function getAllKeywords(): array
    {
        return static::pluck('keyword')->toArray();
    }

    /**
     * Bulk insert keywords (ignoring duplicates)
     *
     * @param array $keywords
     * @return int Number of inserted records
     */
    public static function bulkInsert(array $keywords): int
    {
        $inserted = 0;

        foreach ($keywords as $keyword) {
            try {
                static::create(['keyword' => trim($keyword)]);
                $inserted++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore duplicate entry errors (1062 is MySQL duplicate key error code)
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
            }
        }

        return $inserted;
    }
}
