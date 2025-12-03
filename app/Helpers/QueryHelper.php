<?php

namespace App\Helpers;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class QueryHelper
{
    /**
     * Apply filters to a model and retrieve results.
     *
     * @param  Model $model
     * @param  array $filters
     * @return Collection
     */
    public static function findWhere(Model $model, array $filters = []): Collection
    {
        $query = $model->query();
        foreach ($filters as $field => $value) {
            if (!is_null($value)) {
                $query->where($field, $value);
            }
        }
        return $query->get();
    }

    /**
     * Check if a specific value exists for a given field in a model.
     *
     * @param  array $conditions
     * @param  Model $model
     * @return bool
     */
    public static function exists(array $conditions, Model $model): bool
    {
        return $model::where($conditions)->exists();
    }


    /**
     * @throws Exception
     */
    public static function incrementColumn(string $modelClass, array $conditions, string $columnToIncrement): void
    {
        $model = $modelClass::where($conditions)->first();
        if (!$model) {
            throw new Exception("Record not found for incrementing column: $columnToIncrement");
        }
        $model->increment($columnToIncrement);
    }
}
