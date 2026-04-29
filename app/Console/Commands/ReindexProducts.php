<?php

namespace App\Console\Commands;

use App\Services\ProductSearchService;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Console\Command;

class ReindexProducts extends Command
{
    protected $signature = 'search:reindex-products';

    protected $description = 'Create Elasticsearch index and reindex all products';

    /**
     * @throws ServerResponseException
     * @throws ClientResponseException
     * @throws MissingParameterException
     */
    public function handle(ProductSearchService $service): int
    {
        $this->info('Creating index...');
        $service->createIndex();

        $this->info('Indexing products...');
        $service->indexAll();

        $this->info('Done.');

        return self::SUCCESS;
    }
}
