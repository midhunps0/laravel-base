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
        array $advSearch,
        string $selectedIds,
        string $resultsName = 'results'
    ): array;

    public function processDownload(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string $selectedIds
    ): array;

    public function getIdsForParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
    ): array;

    public function getQueryAndParams(
        array $searches,
        array $sorts,
        array $filters,
        array $advSearch,
        string $selectedIds = ''
    ): array;

    public function getItem(string $search): Collection;
}
?>