<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->itemRepository->pushCriteria(new RequestCriteria($request));
        $items = $this->itemRepository->all();

        return view('items.index')
            ->with('items', $items);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'category' => 'nullable',
            'brand' => 'nullable',
            'color' => 'nullable',
            'picture' => 'required',
            'condition' => 'required|in:NEW,USED',
        ]);
                
        $input = $request->all();

        $item = $this->itemRepository->create($input);

        Flash::success('Item saved successfully.');

        return redirect(route('items.index'));



        Item::create($validatedData);

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        $item = $this->itemRepository->findWithoutFail($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        return view('items.show')->with('item', $item);
    }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $item = $this->itemRepository->findWithoutFail($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        return view('items.edit')->with('item', $item);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'category' => 'nullable',
            'brand' => 'nullable',
            'color' => 'nullable',
            'picture' => 'required',
            'condition' => 'required|in:NEW,USED',
            // Add validation rules for other fields
        ]);

        $item->update($validatedData);

        Flash::success('Item updated successfully.');

        return redirect(route('items.index'));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item = $this->itemRepository->findWithoutFail($id);

        if (empty($item)) {
            Flash::error('Item not found');

            return redirect(route('items.index'));
        }

        $this->itemRepository->delete($id);

        Flash::success('Item deleted successfully.');

        return redirect(route('items.index'));

    }
}
