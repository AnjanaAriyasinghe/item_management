<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectRequest;
use App\Http\Requests\VendorRequest;
use App\Models\Payment;
use App\Models\VenderHasAccount;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class VendorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-vendor-module|admin-common-vendor-create')->only('index');
        $this->middleware('permission:admin-common-vendor-edit')->only(['edit', 'update']);
        $this->middleware('permission:admin-common-vendor-delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Vendor::with('approved_by_user','rejected_by_user')->latest('id');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('created_at', function ($data) {
                        return $data->updated_at ?? $data->created_at;
                    })
                    ->addColumn('status', function ($data) {
                        // Define status and corresponding badge classes
                        $status = $data->is_active;
                        switch ($status) {
                            case '0':
                                $badge = '<span class="badge bg-warning">Pending</span>';
                                break;
                            case '1':
                                $badge = '<span class="badge bg-success-1">Active</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-danger">Rejected</span>';
                                break;
                        }

                        return $badge;
                    })
                    ->addColumn('approved_by', function ($data) {
                       return $data->approved_by_user?$data->approved_by_user->name:"-";
                    })
                    ->addColumn('rejected_by', function ($data) {
                       return $data->rejected_by_user?$data->rejected_by_user->name:"-";
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-vendor-edit')) {
                            $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-vendor-view')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-vendor-approve')) {
                            if ($data->is_active == 0 | $data->is_active == 2) {
                                $buttons .= ' <button class="btn btn-success btn-sm btnApproval"  data-bs-toggle="modal" data-bs-target="#approvalModel" title="Approval" data-id=\'' . json_encode($data->id) . '\'><i class="ti ti-check"></i></button>';
                            }
                        }
                        if ($data->is_active == 1 | $data->is_active == 0) {
                            if (auth()->user()->can('admin-common-vendor-deactivate')) {
                                if (!Payment::where('vendor_id', $data->id)->exists()) {
                                    $buttons .= ' <button class="btn btn-danger btn-sm btnReject"  data-bs-toggle="modal" data-bs-target="#rejectModel" title="Reject" data-id=\'' . json_encode($data->id) . '\'><i class="ti ti-x"></i></button>';
                                }
                            }
                        }
                        if (auth()->user()->can('admin-common-vendor-delete')) {
                            if (!Payment::where('vendor_id', $data->id)->exists()) {
                                $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.vendors.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                            }
                        }
                        return $buttons;
                    })
                    ->rawColumns(['status', 'action','approved_by','rejected_by'])
                    ->make(true);
            }
            return view('pages.admin.vendor.index');
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
    public function store(VendorRequest $request)
    {
        try {
            $account_numbers = $request->account_numbers;
            $account_nu_array = [];
            $mobile_numbers = $request->mobile_numbers;
            $mobile_nu_array = [];
            foreach ($account_numbers as $key => $value) {
                if (is_null($value)) {
                    $account_nu_array[$key] = 0;
                } else {
                    $account_nu_array[$key] = $value;
                }
            }
            foreach ($mobile_numbers as $key => $value) {
                if (is_null($value)) {
                    $mobile_nu_array[$key] = 0;
                } else {
                    $mobile_nu_array[$key] = $value;
                }
            }
            DB::beginTransaction();
            $input = $request->all();
            $input['created_by'] = auth()->user()->id;
            $vendor = Vendor::create($input);
            if ($vendor) {
                if ($account_nu_array != null | $mobile_nu_array != null) {
                    foreach ($account_nu_array as $key => $value) {
                        if ($account_nu_array[$key] == 0) {
                            $account_nu_array[$key] = null;
                        } elseif ($mobile_nu_array[$key] == 0) {
                            $mobile_nu_array[$key] = null;
                        }
                        if ($mobile_nu_array[$key] != null &&  $account_nu_array[$key] != null) {
                            VenderHasAccount::create([
                                'vendor_id' => $vendor->id,
                                'account_number' => $account_nu_array[$key],
                                'mobile' => $mobile_nu_array[$key],
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            if ($vendor) {
                return response()->json(['message' => "Vendor created successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vendor $vendor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vendor $vendor)
    {
        try {
            $vendor = Vendor::with('accounts')->find($vendor->id);
            return response()->json([
                'vendor' => $vendor,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VendorRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $account_numbers = $request->account_numbers;
            $account_nu_array = [];
            $mobile_numbers = $request->mobile_numbers;
            $mobile_nu_array = [];
            foreach ($account_numbers as $key => $value) {
                if (is_null($value)) {
                    $account_nu_array[$key] = 0;
                } else {
                    $account_nu_array[$key] = $value;
                }
            }
            foreach ($mobile_numbers as $key => $value) {
                if (is_null($value)) {
                    $mobile_nu_array[$key] = 0;
                } else {
                    $mobile_nu_array[$key] = $value;
                }
            }
            $vendor = Vendor::findOrFail($id);
            $input = $request->all();
            $input['updated_by'] = auth()->user()->id;
            $input['is_active'] = "0";
            $vendor->update($input);
            $vendor->accounts()->delete($id);
            if ($vendor) {
                if ($account_nu_array != null | $mobile_nu_array != null) {
                    foreach ($account_nu_array as $key => $value) {
                        if ($account_nu_array[$key] == 0) {
                            $account_nu_array[$key] = null;
                        } elseif ($mobile_nu_array[$key] == 0) {
                            $mobile_nu_array[$key] = null;
                        }
                        if ($mobile_nu_array[$key] != null &&  $account_nu_array[$key] != null) {
                            VenderHasAccount::create([
                                'vendor_id' => $vendor->id,
                                'account_number' => $account_nu_array[$key],
                                'mobile' => $mobile_nu_array[$key],
                            ]);
                        }
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => "Vendor updated successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vendor $vendor)
    {
        try {
            $vendor->update(['deleted_by' => auth()->user()->id]);
            $vendor->delete();
            if ($vendor) {
                return response()->json(['message' => "Vendor deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }


    public function approved(Request $request)
    {
        try {
            $vendor =Vendor::find($request->id);
            $vendor->update(['is_active' => '1' ,
            'approved_by' =>auth()->user()->id ,
            'approved'=>now(),
            'approved_remark'=>$request->comment,
            'rejected_by' =>null,
            'rejected'=>null,
            'rejected_remark'=>null
        ]);
            if ($vendor) {
                return response()->json(['message' => "Vendor activate successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
    public function rejected(RejectRequest $request){
        try {
            $vendor =Vendor::find($request->id);
            $vendor->update(['is_active' => '2' ,
            'rejected_by' =>auth()->user()->id ,
            'rejected'=>now(),
            'rejected_remark'=>$request->comment,
            'approved_by' =>null,
            'approved'=>null,
            'approved_remark'=>null,
        ]);
            if ($vendor) {
                return response()->json(['message' => "Vendor rejected successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function get_vendor_accounts($id){
        try {
           return Vendor::with('accounts')->find($id);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
