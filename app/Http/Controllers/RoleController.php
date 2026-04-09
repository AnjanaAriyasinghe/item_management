<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Admin\app\Http\Requests\CreateUserRoleRequest;
use Modules\Admin\app\Http\Requests\UpdateUserRoleRequest;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-user_roles-module|admin-common-user_roles-create')->only('index');
        $this->middleware('permission:admin-common-user_roles-edit')->only(['edit','update']);
        $this->middleware('permission:admin-common-user_roles-delete')->only('destroy');
    }
    public function index(Request $request)
    {
        try {

            $superAdminRoleId = Role::where('name', 'Super Admin')->first()->id;
            if ($request->ajax()) {
                if (\auth()->user()->hasRole('Super Admin')) {
                    $data = Role::with('permissions');
                } else {
                    $data = Role::where('id', '!=', $superAdminRoleId)->with('permissions');
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-user_roles-edit')) {
                        $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                        }
                        if (auth()->user()->can('admin-common-user_roles-delete')) {
                        $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.roles.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
            $permissions = $this->BuildPermissionTree();
            return view('pages.admin.role.index', compact('permissions'));
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = $this->BuildPermissionTree();
        return view('pages.admin.role.index', compact('permissions'));
    }
    public function BuildPermissionTree()
    {
        try {
            $permissions = Permission::all();
            $permission_categories = [];
            foreach ($permissions as $permission) {

                $name = explode('-', $permission->name);

                $main = $name[0];
                $key = $name[1];
                $parent = $name[2] ?? null;
                $child = $name[3] ?? null;

                if (!array_key_exists($key, $permission_categories)) {
                    if (!array_key_exists($parent, $permission_categories)) {
                        $permission_categories[$main][$key][$parent][] = [
                            'id' => $permission->id,
                            'name' => $child
                        ];
                    } else {
                        array_push($permission_categories[$main][$key][$parent], [
                            'id' => $permission->id,
                            'name' => $child
                        ]);
                    }
                } else {
                    if (!array_key_exists($parent, $permission_categories)) {
                        $permission_categories[$main][$key][$parent][] = [
                            'id' => $permission->id,
                            'name' => $child
                        ];
                    } else {
                        if (!isset($permission_categories[$main][$key][$parent])) {
                            $permission_categories[$main][$key][$parent] = [];
                        }
                        // Use array_push to add the permission
                        array_push($permission_categories[$main][$key][$parent], [
                            'id' => $permission->id,
                            'name' => $child
                        ]);
                    }
                }
            }
            return $permission_categories;
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        try {
            DB::beginTransaction();
            $role = Role::create(['name' => $request->name]);
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
            DB::commit();
            if ($role) {
                return response()->json(['message' => "Role created successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json([
            'role' => $role,
            'permissions' => $role->permissions->pluck('id')
        ]);
    }

    public function update(StoreRoleRequest $request, $id)
    {
        try {
            $input = $request->only('name');
            $role = Role::with('permissions')->findOrFail($id);
            $role->update($input);
            $permissionIds = $request->permissions;
            $permissions = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray(); // Get permission names
            $role->syncPermissions($permissions);
            if ($role) {
                return response()->json(['message' => "Role updated successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        try {
            if ($role->name == 'Super Admin') {
                abort(403, 'SUPER ADMIN ROLE CAN NOT BE DELETED');
            }
            if (auth()->user()->hasRole($role->name)) {
                abort(403, 'CAN NOT DELETE SELF ASSIGNED ROLE');
            }
            $role->delete();
            if ($role) {
                return response()->json(['message' => "Role deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
