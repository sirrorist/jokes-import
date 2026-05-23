<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListExternalRecordsRequest;
use App\Http\Resources\ExternalRecordResource;
use App\Models\ExternalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use OpenApi\Attributes as OA;

#[OA\Info(version: '1.0.0', title: 'Amo Point API')]
#[OA\Tag(name: 'Jokes', description: 'Imported external records')]
class ExternalRecordController extends Controller
{
    #[OA\Get(
        path: '/api/jokes',
        description: 'Returns imported jokes from the database',
        summary: 'List imported jokes',
        tags: ['Jokes'],
        parameters: [
            new OA\Parameter(name: 'source', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 15)),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\Parameter(name: 'limit', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'offset', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful response'),
        ]
    )]
    public function index(ListExternalRecordsRequest $request): AnonymousResourceCollection|JsonResponse
    {
        $query = ExternalRecord::query()->latest('id');

        if ($request->filled('source')) {
            $query->where('source', $request->string('source')->toString());
        }

        if ($request->filled('limit')) {
            $records = $query
                ->offset((int) $request->integer('offset', 0))
                ->limit((int) $request->integer('limit'))
                ->get();

            return ExternalRecordResource::collection($records)
                ->additional([
                    'meta' => [
                        'limit' => (int) $request->integer('limit'),
                        'offset' => (int) $request->integer('offset', 0),
                        'count' => $records->count(),
                    ],
                ]);
        }

        $perPage = (int) $request->integer('per_page', 15);

        return ExternalRecordResource::collection(
            $query->paginate($perPage)->withQueryString()
        );
    }
}
