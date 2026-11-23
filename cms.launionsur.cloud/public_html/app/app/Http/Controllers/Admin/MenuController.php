<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Menu::class);
        $menus = Menu::with('items')->orderBy('name')->get();
        return Inertia::render('Menus/Index', ['menus' => $menus]);
    }

    public function __construct()
    {
        $this->middleware('permission:edit');
    }

    public function create()
    {
        $this->authorize('create', Menu::class);
        return Inertia::render('Menus/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'slug' => 'required|string|max:255']);
        Menu::create($data);
        return redirect()->route('admin.menus.index')->with('success', 'Menu created');
    }

    public function edit(Menu $menu)
    {
        $this->authorize('update', $menu);
        $menu->load('items');
        return Inertia::render('Menus/Edit', ['menu' => $menu]);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate(['name' => 'required|string|max:255', 'slug' => 'required|string|max:255']);
        $menu->update($data);
        return redirect()->route('admin.menus.index')->with('success', 'Menu updated');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu deleted');
    }

    // Menu Items
    public function addItem(Request $request, Menu $menu)
    {
        $data = $request->validate(['title' => 'required|string|max:255', 'url' => 'nullable|string|max:255', 'parent_id' => 'nullable|integer']);
        $data['menu_id'] = $menu->id;
        $data['position'] = MenuItem::where('menu_id', $menu->id)->max('position') + 1;
        MenuItem::create($data);
        return redirect()->route('admin.menus.edit', $menu->id)->with('success', 'Item added');
    }

    public function deleteItem(Menu $menu, MenuItem $item)
    {
        $item->delete();
        return redirect()->route('admin.menus.edit', $menu->id)->with('success', 'Item removed');
    }

    /**
     * Accepts an ordered array of item ids to reorder positions.
     */
    public function reorderItems(Request $request, Menu $menu)
    {
        $this->authorize('update', $menu);
        $ids = $request->input('order', []);
        if (!is_array($ids)) {
            return response()->json(['error' => 'Invalid payload'], 422);
        }

        foreach ($ids as $pos => $id) {
            MenuItem::where('menu_id', $menu->id)->where('id', $id)->update(['position' => $pos + 1]);
        }

        return response()->json(['status' => 'ok']);
    }
}
