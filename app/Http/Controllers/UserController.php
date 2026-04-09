<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdatePasswordRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Company;
use App\Models\User;
use App\Models\UserhasCompany;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    use ImageUpload;
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-users-module|admin-common-users-create')->only('index');
        $this->middleware('permission:admin-common-users-edit')->only(['edit', 'update']);
        $this->middleware('permission:admin-common-users-delete')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $superAdminRoleId = Role::where('name', 'Super Admin')->first()->id;
            if ($request->ajax()) {
                if (\auth()->user()->hasRole('Super Admin')) {
                    $data = User::with('roles')->latest('id');
                } else {
                    $data = User::whereDoesntHave('roles', function ($query) use ($superAdminRoleId) {
                        $query->where('id', $superAdminRoleId);
                    })->latest('id')
                        ->with('roles');
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
                    ->addColumn('role', function ($user) {
                        $roles = $user->roles->pluck('name')->map(function ($role) {
                            return '<span class="badge bg-success text-center">' . $role . '</span>';
                        })->implode(' ');

                        return $roles ?? '';
                    })
                    ->addColumn('image', function ($data) {
                        if ($data->image) {
                            $imageUrl = asset('storage/' . $data['image']);
                            $html = '<div class="image-zoom-container">';
                            $html .= '<img src="' . $imageUrl . '" alt="Image" class="wid-50 rounded-circle">';
                            $html .= '</div>';
                        } else {
                            $html = '<div class="image-zoom-container">';
                            $html = '<img src="' . asset('build/images/user/avatar-2.jpg') . '" alt="user-image" class="wid-50 rounded-circle" />';
                            $html .= '</div>';
                        }
                        return $html;
                    })
                    ->addColumn('default_company_name', function ($user) {
                        $companyName = $user->defaultCompany?->name;
                        // dd($companyName);

                        if ($companyName) {
                            return '<div class="d-flex flex-wrap align-items-start">' .
                                '<span class="badge bg-success text-center mb-1 me-1">' . $companyName . '</span>' .
                                '</div>';
                        }

                        return '';
                    })
                    ->addColumn('company', function ($user) {

                        $companies = $user->companies?->pluck('name')->map(function ($company) {
                            return '<span class="badge bg-success text-center mb-1 me-1">' . $company . '</span>';
                        })->implode('');

                        return $companies ? '<div class="d-flex flex-wrap align-items-start">' . $companies . '</div>' : '';
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-users-edit')) {
                            $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-users-delete')) {
                            if($data->id !=1){
                                $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.users.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                            }
                        }
                        if (auth()->user()->can('admin-common-users-edit')) {
                            $buttons .= ' <Button class="btn btn-primary btn-sm btnUpdate" data-bs-toggle="modal" data-bs-target="#passwordReset" title="Update Password" data-id=\'' . json_encode($data->id) . '\'> <i class="ph-duotone ph-key"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'image','company','default_company_name'])
                    ->make(true);
            }
            $roles = Role::whereNotIn('id', [1])->get();
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies = Company::all();
            }
            return view('pages.admin.user.index', ['roles' => $roles ,'companies'=>$companies]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
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
    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            $input = $request->validated();

            $input['image'] = $this->save_image($request->file('image'), 'user_image', null);
            $input['password'] = Hash::make($request->password);
            $input['created_by'] = auth()->user()->id;
            $user = User::create($input);

            // dd($request);
            $user->companies()->attach($request->companies);

            $role = Role::where('id', $request->roles)->get();
            $user->assignRole($role);
            DB::commit();
            return response()->json(['message' => "User created successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        try {
            $user_data = User::find($user->id);
            $user_companies = $user->companies->pluck('id')->toArray();
            $all_companies = Company::all();
            // dd($user_data);
            return response()->json([
                'user' => $user->roles[0],
                'user_data' => $user_data,
                'all_companies' => $all_companies,
                'user_companies' => $user_companies,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, string $id)
    {
        try {
            // dd($request);
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $input = $request->validated();
            $input['updated_by'] = auth()->user()->id;
            if ($request->file('image')) {
                $input['image'] = $this->save_image($request->file('image'), 'user_image', null);
            }
            $user->update($input);
            $user->companies()->sync($request->companies);

            $role = Role::find($request->roles);
            $user->syncRoles($role->name);
            DB::commit();

            return response()->json(['message' => "User updated successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            if (Auth::user()->id == $user->id) {
                return response()->json(['message' => "CANNOT DELETE YOUR OWN ACCOUNT.....", 'status' => false], 500);
            } else {
                $user->update(['deleted_by' => auth()->user()->id]);
                $user->delete();
                if ($user) {
                    return response()->json(['message' => "User deleted successfully...", 'status' => true], 200);
                }
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function update_password(UserUpdatePasswordRequest $request)
    {
        try {
            $user = User::find($request->user_id);
            $user->password= Hash::make($request->password);
            $user->save();
            if($user){
                return response()->json(['message' => "User password updated successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function update_default_company(User $user, Request $request)
    {
        try {
            $user->default_company = $request->default_company;
            $user->save();

            if ($user) {
                return redirect()->back()->with('success', 'Default company updated successfully.');
            }

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Failed to update default company: ' . $th->getMessage());
        }
    }
}
