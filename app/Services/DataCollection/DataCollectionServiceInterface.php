<?php

namespace App\Services\DataCollection;

interface DataCollectionServiceInterface
{
    /**
     * Fetch problems/ideas from the source.
     *
     * @param int $limit
     * @return array
     */
    public function fetch(int $limit = 10): array;
}
