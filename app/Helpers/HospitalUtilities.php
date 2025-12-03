<?php

if (!function_exists('createCharge')) {
    function createCharge(string $name, string $amount, string $hospitalId): array
    {
        return [
            'hospital_id' => $hospitalId,
            'charge_name' => $name,
            'charge_id' => generateCustomId($name, 3, 4),
            'created_at' => now(),
            'amount' => $amount,
        ];
    }
}


if (!function_exists('createStaffCategory')) {
    /**
     * @param  string      $name
     * @param  string|null $categoryId
     * @param  string      $hospitalId
     * @param  int         $staffCount
     * @return array
     */
    function createStaffCategory(string $name, string $categoryId = null, string $hospitalId, int $staffCount = 0): array
    {
        return [
            'category_name' => $name,
            'no_of_staff' => $staffCount,
            'category_id' => $categoryId ?: generateCustomId($name, 3, 5),
            'hospital_id' => $hospitalId,
            'created_at' => now(),
        ];
    }
}
