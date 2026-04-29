<?php

namespace App\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductSearchServiceInterface
{
    public function search(array $filters): LengthAwarePaginator;

    public function createIndex(): void;

    public function indexAll(): void;
}
