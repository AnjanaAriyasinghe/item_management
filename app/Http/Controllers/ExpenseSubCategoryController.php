<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseSubCategoryRequest;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class ExpenseSubCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-expense_categories-module|admin-common-expense_categories-create')->only('index');
        $this->middleware('permission:admin-common-expense_categories-edit')->only(['edit', 'update']);
        $this->middleware('permission:admin-common-expense_categories-delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                if($request->category_id){
                    $data = ExpenseSubCategory::with('category','created_user')
                    ->when($request->category_id != 'all', function ($query) use ($request) {
                        $query->whereHas('category', function ($query) use ($request) {
                            $query->where('category_id', $request->category_id);
                        });
                    });
                }else{
                    $data=[];
                }
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
                        if (auth()->user()->can('admin-common-expense_categories-edit')) {
                            $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-expense_categories-delete')) {
                            $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.expense_sub_categories.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'image'])
                    ->make(true);
            }
            return view('pages.admin.expenseCategory.sub_category');
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
    public function store(ExpenseSubCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $input = $request->all();
            $input['created_by'] = auth()->user()->id;
            $expenseSubCategory = ExpenseSubCategory::create($input);
            DB::commit();
            if ($expenseSubCategory) {
                return response()->json(['message' => "Expense Sub Category created successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseSubCategory $expenseSubCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExpenseSubCategory $expenseSubCategory)
    {
        try {
            $expenseSubCategory = ExpenseSubCategory::find($expenseSubCategory->id);
            return response()->json([
                'su_category' => $expenseSubCategory,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        try {
            DB::beginTransaction();
            $expenseCategory = ExpenseSubCategory::findOrFail($id);
            $input = $request->all();
            $input['updated_by']=auth()->user()->id;
            $expenseCategory->update($input);
            DB::commit();
            return response()->json(['message' => "Expense Sub Category updated successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseSubCategory $expenseSubCategory)
    {
        try {
            $expenseSubCategory->update(['deleted_by' => auth()->user()->id]);
            $expenseSubCategory->delete();
            if ($expenseSubCategory) {
                return response()->json(['message' => "Expense Sub Category deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function getcategory(Request  $request){
        try {
            $categories = ExpenseCategory::all();
            return response()->json([
                'categories' => $categories,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
