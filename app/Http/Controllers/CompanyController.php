<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyRequest;
use App\Models\Company;
use App\Models\UserhasCompany;
use App\Traits\ImageUpload;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    use ImageUpload;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-company-module|admin-common-company-create')->only('index');
        $this->middleware('permission:admin-common-company-edit')->only(['edit', 'update']);
    }
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = Company::with('updated_by')->latest('id');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('logo', function ($data) {
                        if ($data->logo) {
                            $imageUrl = asset('storage/' . $data['logo']);
                            $html = '<div class="image-zoom-container">';
                            $html .= '<img src="' . $imageUrl . '" alt="Image" class="wid-100">';
                            $html .= '</div>';
                        } else {
                            $html = '<div class="image-zoom-container">';
                            $html = '<img src="' . asset('build/images/user/avatar-2.jpg') . '" alt="user-image" class="wid-10 rounded-circle" />';
                            $html .= '</div>';
                        }
                        return $html;
                    })
                    ->addColumn('created_at', function ($data) {
                        return $data->updated_at ?? $data->created_at;
                    })



                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-company-edit')) {
                            $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['action', 'logo'])
                    ->make(true);
            }
        
            return view('pages.admin.company.index');
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
    public function store(CompanyRequest $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->validated();

            $input['updated_by'] = auth()->user()->id;
            $input['logo'] = $this->save_image($request->file('logo'), 'user_image', null);

            $company = Company::create($input);
            DB::commit();
            return response()->json(['message' => "Company created successfully...", 'status' => true], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {

        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        try {
            return Company::findOrFail($company->id);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CompanyRequest $request, $id)
    {
        try {
            $input = $request->all();
            $input['updated_by'] = auth()->user()->id;
            $company = Company::findOrFail($id);
            if ($request->file('logo')) {
                $input['logo'] = $this->save_image($request->file('logo'), 'company_logo', null);
            }
            $company->update($input);
            return response()->json(['message' => "Company updated successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        //
    }
}
