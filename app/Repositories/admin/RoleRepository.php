<?php

namespace App\Repositories\admin;

use App\Exceptions\GeneralException;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Repositories\BaseRepository;

class RoleRepository extends BaseRepository
{
    public function model()
    {
        return Role::class;
    }

    public function store(array $data, array $permissions)
    {
        try {
            $role = Role::create($data);
            if ($role) {
                $role_permissions = [];
                foreach ($permissions as $permission) {
                    $role_permissions[] = ['role_id' => $role->id, 'permission_id' => $permission];
                }
                RolePermission::insert($role_permissions);
            }

            return $role;
        } catch (GeneralException $e) {
            throw new GeneralException('server error');
        }
    }

    public function update($id, array $data, array $permissions)
    {
        try {
            $role = Role::find($id);
            if ($role) {
                $role->update($data);
                $role_permissions = [];
                foreach ($permissions as $permission) {
                    $role_permissions[] = ['role_id' => $role->id, 'permission_id' => $permission];
                }
                RolePermission::where('role_id', $role->id)->delete();
                RolePermission::insert($role_permissions);
            }
        } catch (GeneralException $e) {
            throw new GeneralException('server error');
        }
    }

    public function show($id)
    {
        $role = $this->model->with(['permissions'])->find($id);

        return $role;
    }

    public function permissions()
    {
        $result = Permission::all();

        return $result;
    }
}
