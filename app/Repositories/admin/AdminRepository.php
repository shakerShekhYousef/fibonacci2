<?php

namespace App\Repositories\admin;

use App\Exceptions\GeneralException;
use App\Models\Role;
use App\Models\User;
use App\Repositories\BaseRepository;

class AdminRepository extends BaseRepository
{
    public function model()
    {
        return User::class;
    }

    public function create(array $data)
    {
        try {
            $data['account_type'] = 'admin';
            $admin = $this->model->create($data);
        } catch (GeneralException $e) {
            throw new GeneralException('server error');
        }
    }

    public function all(array $columns = ['*'])
    {
        return $this->model->with(['role'])->where('account_type', 'admin')->get();
    }

    public function roles()
    {
        return Role::all();
    }
}
