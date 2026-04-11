<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Customer::latest('id');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        $buttons .= '<button class="btn btn-warning btn-sm btnEdit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createModel"
                                        data-id=\'' . json_encode($data->id) . '\'>
                                        <i class="ti ti-edit f-20"></i>
                                    </button> ';
                        $buttons .= '<button class="btn btn-danger btn-sm"
                                        onclick="handleDelete(\'' . route('admin.customers.destroy', $data->id) . '\', { _token: \'' . csrf_token() . '\' })">
                                        <i class="ti ti-trash f-20"></i>
                                    </button>';
                        return $buttons;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            return view('pages.admin.customer.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(CustomerRequest $request)
    {
        try {
            $input = $request->all();
            $input['created_by'] = auth()->id();
            $customer = Customer::create($input);

            if ($customer) {
                return response()->json(['message' => 'Customer created successfully.', 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function show(Customer $customer)
    {
        //
    }

    public function edit(Customer $customer)
    {
        try {
            return response()->json(['customer' => $customer]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        try {
            $input = $request->all();
            $input['updated_by'] = auth()->id();
            $customer->update($input);
            return response()->json(['message' => 'Customer updated successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->update(['deleted_by' => auth()->id()]);
            $customer->delete();
            return response()->json(['message' => 'Customer deleted successfully.', 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
