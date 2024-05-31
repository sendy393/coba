<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::paginate(10);
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:items|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item = Item::create($request->all());
        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:items,name,' . $item->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $item->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $query = $request->get('query', '');
        $items = Item::where('name', 'LIKE', "%{$query}%")->paginate(10);
        return response()->json($items);
    }
}
