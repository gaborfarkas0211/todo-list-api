<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateItemRequest;
use App\Http\Requests\Api\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = $this->getItemsPaginator($request);

        return $this->sendSuccess(ItemResource::collection($items)->response()->getData(true));
    }

    public function show(Item $item): JsonResponse
    {
        if ($item = Item::get($item)) {
            return $this->sendSuccess($item);
        }

        return $this->sendError();
    }

    public function store(CreateItemRequest $request): JsonResponse
    {
        $item = new Item($request->only(['name', 'description']));
        $item->save();

        return $this->sendSuccess($item->refresh());
    }

    public function update(UpdateItemRequest $request, Item $item): JsonResponse
    {
        $item->update($request->only(['name', 'description', 'completed']));

        return $this->sendSuccess($item->refresh());
    }

    public function destroy(Item $item): JsonResponse
    {
        $item->delete();

        return $this->sendSuccess();
    }

    private function getItemsPaginator(Request $request): LengthAwarePaginator
    {
        $page = $request->query('page', self::PAGE);
        $perPage = $request->query('per_page', self::PAGE_LIMIT);
        $filters = [
            'name' => $request->query('name'),
            'completed' => $request->query('completed')
        ];

        return Item::getItemsQuery($filters)->paginate($perPage, '*', 'items_page', $page);
    }
}
