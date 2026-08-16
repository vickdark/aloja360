<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Accommodation;
use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InventoryItemController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', InventoryItem::class);
        $inventoryItems = InventoryItem::with('accommodation')->orderBy('category')->orderBy('name')->paginate(20);
        return view('inventory.index', compact('inventoryItems'));
    }

    public function create()
    {
        $this->authorize('create', InventoryItem::class);
        $accommodations = Accommodation::orderBy('name')->pluck('name', 'id');
        return view('inventory.create', compact('accommodations'));
    }

    public function store(StoreInventoryItemRequest $request)
    {
        $this->authorize('create', InventoryItem::class);
        $data = $request->validated();
        $data['is_consumable'] = $request->boolean('is_consumable', false);
        InventoryItem::create($data);
        return redirect()->route('inventory.index')->with('success', 'Ítem de inventario creado correctamente.');
    }

    public function show(InventoryItem $inventoryItem)
    {
        $this->authorize('view', $inventoryItem);
        return view('inventory.show', compact('inventoryItem'));
    }

    public function edit(InventoryItem $inventoryItem)
    {
        $this->authorize('update', $inventoryItem);
        $accommodations = Accommodation::orderBy('name')->pluck('name', 'id');
        return view('inventory.edit', compact('inventoryItem', 'accommodations'));
    }

    public function update(UpdateInventoryItemRequest $request, InventoryItem $inventoryItem)
    {
        $this->authorize('update', $inventoryItem);
        $data = $request->validated();
        $data['is_consumable'] = $request->boolean('is_consumable', false);
        $inventoryItem->update($data);
        return redirect()->route('inventory.index')->with('success', 'Ítem de inventario actualizado correctamente.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $this->authorize('delete', $inventoryItem);
        $inventoryItem->delete();
        return redirect()->route('inventory.index')->with('success', 'Ítem de inventario eliminado correctamente.');
    }
}
