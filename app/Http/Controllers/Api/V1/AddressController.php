<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Commerce\AddressRequest;
use App\Http\Resources\Commerce\AddressResource;
use App\Models\Address;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/** @group Customer addresses */
class AddressController extends ApiController
{
    /** List the customer's delivery addresses. */
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()->addresses()->orderByDesc('is_default')->latest()->get();

        return $this->success(AddressResource::collection($addresses)->resolve(), 'Addresses retrieved successfully.');
    }

    /** Create a delivery address. */
    public function store(AddressRequest $request): JsonResponse
    {
        $address = DB::transaction(function () use ($request): Address {
            $data = $request->validated();
            $makeDefault = ($data['is_default'] ?? false) || ! $request->user()->addresses()->exists();

            if ($makeDefault) {
                $request->user()->addresses()->update(['is_default' => false]);
            }

            return $request->user()->addresses()->create([...$data, 'is_default' => $makeDefault]);
        });

        return $this->success((new AddressResource($address))->resolve(), 'Address created successfully.', 201);
    }

    /** Update an owned delivery address. */
    public function update(AddressRequest $request, Address $address): JsonResponse
    {
        Gate::authorize('update', $address);

        DB::transaction(function () use ($request, $address): void {
            $data = $request->validated();

            if ($data['is_default'] ?? false) {
                $request->user()->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }

            $address->update($data);
        });

        return $this->success((new AddressResource($address->refresh()))->resolve(), 'Address updated successfully.');
    }

    /** Delete an owned delivery address. */
    public function destroy(Request $request, Address $address): JsonResponse
    {
        Gate::authorize('delete', $address);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $request->user()->addresses()->latest()->first()?->update(['is_default' => true]);
        }

        return $this->success(null, 'Address deleted successfully.');
    }
}
