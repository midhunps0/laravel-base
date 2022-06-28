<?php
namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ModelViewConnector
{
    public function index(
        int $itemsCount,
        int $page,
        array $searches,
        array $sorts,
        array $filters,
        string $selectedIds,
        string $resultsName = 'results'
    ): array;

    public function processDownload(
        array $searches,
        array $sorts,
        array $filters,
        string $selectedIds
    ): Collection;

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters
    ): array;

    public function getQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        string $selectedIds = ''
    ): array;

    public function getItem(string $search): Collection;
}
?>