<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCategoryRequest;
use App\Http\Requests\ExpenseRequest;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-expense_sub_categories-module|admin-common-expense_sub_categories-create')->only('index');
        $this->middleware('permission:admin-common-expense_sub_categories-edit')->only(['edit', 'update']);
        $this->middleware('permission:admin-common-expense_sub_categories-delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = ExpenseCategory::with('created_user');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('created_at', function ($data) {
                        return $data->updated_at ?? $data->created_at;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-expense_sub_categories-edit')) {
                            $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-expense_sub_categories-delete')) {
                            $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.expense_categories.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'image'])
                    ->make(true);
            }
            return view('pages.admin.expenseCategory.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ExpenseCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['created_by'] = auth()->user()->id;
            $category = ExpenseCategory::create($input);
            DB::commit();
            if ($category) {
                return response()->json(['message' => "Expense category created successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseCategory $expenseCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseCategory $expenseCategory)
    {
        try {
            $category = ExpenseCategory::find($expenseCategory->id);
            return response()->json([
                'category' => $category,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ExpenseCategoryRequest $request,$id)
    {
        try {
            DB::beginTransaction();
            $expenseCategory = ExpenseCategory::findOrFail($id);
            $input = $request->all();
            $input['updated_by']=auth()->user()->id;
            $expenseCategory->update($input);
            DB::commit();
            return response()->json(['message' => "Expense Category updated successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        try {
            $expenseCategory->update(['deleted_by' => auth()->user()->id]);
            $expenseCategory->delete();
            if ($expenseCategory) {
                return response()->json(['message' => "Expense category deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
