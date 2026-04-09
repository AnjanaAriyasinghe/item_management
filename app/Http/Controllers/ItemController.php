<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Item::with('created_user');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('item_photo', function ($data) {
                        if ($data->item_photo) {
                            return '<img src="' . asset('storage/' . $data->item_photo) . '" alt="Item Photo" width="50" height="50" style="object-fit:cover;border-radius:6px;">';
                        }
                        return '<span class="badge bg-secondary">No Photo</span>';
                    })
                    ->addColumn('created_at', function ($data) {
                        return $data->updated_at ?? $data->created_at;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-items-view')) {
                            $buttons .= '<button class="btn btn-success btn-sm btnView me-1" data-id=\'' . $data->id . '\'><i class="ti ti-eye f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-items-edit')) {
                            $buttons .= '<button class="btn btn-warning btn-sm btnEdit me-1" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'><i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-items-delete')) {
                            $buttons .= '<button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.items.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"><i class="ti ti-trash f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['item_photo', 'action'])
                    ->make(true);
            }
            return view('pages.admin.items.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_no'          => 'required|string|max:255|unique:items,item_no',
            'item_code'        => 'required|string|max:255|unique:items,item_code',
            'item_name'        => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'unit_price'       => 'required|numeric|min:0',
            'item_photo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();
            $input = $request->except('item_photo');
            $input['created_by'] = auth()->user()->id;

            if ($request->hasFile('item_photo')) {
                $path = $request->file('item_photo')->store('items', 'public');
                $input['item_photo'] = $path;
            }

            $item = Item::create($input);

            if ($item) {
                DB::commit();
                return response()->json(['message' => 'Item created successfully.', 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        try {
            $item->load('created_user');
            return response()->json([
                'item'         => $item,
                'photo_url'    => $item->item_photo ? asset('storage/' . $item->item_photo) : null,
                'created_name' => $item->created_user->name ?? '—',
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $request->validate([
            'item_no'          => 'required|string|max:255|unique:items,item_no,' . $id,
            'item_code'        => 'required|string|max:255|unique:items,item_code,' . $id,
            'item_name'        => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'unit_price'       => 'required|numeric|min:0',
            'item_photo'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();
            $input = $request->except('item_photo');
            $input['updated_by'] = auth()->user()->id;

            if ($request->hasFile('item_photo')) {
                // Delete old photo
                if ($item->item_photo) {
                    Storage::disk('public')->delete($item->item_photo);
                }
                $path = $request->file('item_photo')->store('items', 'public');
                $input['item_photo'] = $path;
            }

            $item->update($input);
            DB::commit();

            return response()->json(['message' => 'Item updated successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        try {
            $item->update(['deleted_by' => auth()->user()->id]);
            $item->delete();
            return response()->json(['message' => 'Item deleted successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}

