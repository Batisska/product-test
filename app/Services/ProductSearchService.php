<?php

namespace App\Services;

use App\Models\Product;
use App\Services\Contracts\ProductSearchServiceInterface;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class ProductSearchService implements ProductSearchServiceInterface
{
    public function __construct(
        protected Client $esClient,
    ) {
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $must = [];
        $filter = [];

        if (! empty($filters['q'])) {
            $must[] = ['match' => ['name' => ['query' => $filters['q'], 'operator' => 'and']]];
        }

        if (isset($filters['price_from']) || isset($filters['price_to'])) {
            $range = [];
            if (isset($filters['price_from'])) {
                $range['gte'] = $filters['price_from'];
            }
            if (isset($filters['price_to'])) {
                $range['lte'] = $filters['price_to'];
            }
            $filter[] = ['range' => ['price' => $range]];
        }

        if (! empty($filters['category_id'])) {
            $filter[] = ['term' => ['category_id' => $filters['category_id']]];
        }

        if (isset($filters['in_stock'])) {
            $filter[] = ['term' => ['in_stock' => $filters['in_stock']]];
        }

        if (isset($filters['rating_from'])) {
            $filter[] = ['range' => ['rating' => ['gte' => $filters['rating_from']]]];
        }

        $sort = [];
        $sort[] = match ($filters['sort'] ?? null) {
            'price_asc' => ['price' => 'asc'],
            'price_desc' => ['price' => 'desc'],
            'rating_desc' => ['rating' => 'desc'],
            'newest' => ['created_at' => 'desc'],
            default => ['id' => 'asc'],
        };

        $perPage = $filters['per_page'] ?? 15;
        $page = $filters['page'] ?? 1;
        $from = ($page - 1) * $perPage;

        $params = [
            'index' => 'products',
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => $must,
                        'filter' => $filter,
                    ],
                ],
                'sort' => $sort,
                'from' => $from,
                'size' => $perPage,
            ],
        ];

        $response = $this->esClient->search($params);
        $hits = $response['hits']['hits'];
        $total = $response['hits']['total']['value'] ?? 0;

        $ids = collect($hits)->pluck('_id')->map(fn ($id) => (int) $id);

        if ($ids->isEmpty()) {
            return new Paginator(collect(), 0, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        $items = Product::with('category')->whereIn('id', $ids)->get();

        $ordered = $ids->map(fn ($id) => $items->firstWhere('id', $id))->filter();

        return new Paginator($ordered, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function createIndex(): void
    {
        $indices = $this->esClient->indices();

        $this->esClient->setResponseException(false);
        $indices->delete(['index' => 'products']);
        $this->esClient->setResponseException(true);

        $indices->create([
            'index' => 'products',
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'name' => ['type' => 'text', 'analyzer' => 'russian'],
                        'price' => ['type' => 'float'],
                        'category_id' => ['type' => 'integer'],
                        'in_stock' => ['type' => 'boolean'],
                        'rating' => ['type' => 'float'],
                        'created_at' => ['type' => 'date'],
                        'updated_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ]);
    }

    public function indexAll(): void
    {
        $this->disableIndexRefreshAndReplicas();

        Product::query()->chunk(1000, function ($products) {
            $body = [];

            foreach ($products as $product) {
                $body[] = [
                    'index' => [
                        '_index' => 'products',
                        '_id' => $product->id,
                    ],
                ];

                $body[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'category_id' => $product->category_id,
                    'in_stock' => (bool) $product->in_stock,
                    'rating' => (float) $product->rating,
                    'created_at' => $product->created_at?->toIso8601String(),
                    'updated_at' => $product->updated_at?->toIso8601String(),
                ];
            }

            $response = $this->esClient->bulk(['body' => $body]);

            if ($response['errors'] ?? false) {
                logger()->error('Elasticsearch bulk indexing errors', ['response' => $response]);
            }
        });

        $this->restoreIndexRefreshAndReplicas();
    }

    private function disableIndexRefreshAndReplicas(): void
    {
        $this->esClient->indices()->putSettings([
            'index' => 'products',
            'body' => [
                'index' => [
                    'refresh_interval' => '-1',
                    'number_of_replicas' => 0,
                ],
            ],
        ]);
    }

    private function restoreIndexRefreshAndReplicas(): void
    {
        $this->esClient->indices()->putSettings([
            'index' => 'products',
            'body' => [
                'index' => [
                    'refresh_interval' => '1s',
                    'number_of_replicas' => 1,
                ],
            ],
        ]);

        $this->esClient->indices()->refresh(['index' => 'products']);
    }
}
