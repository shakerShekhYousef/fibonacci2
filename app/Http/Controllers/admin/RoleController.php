<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\Roles\CreateRoleRequest;
use App\Repositories\admin\RoleRepository;

class RoleController extends Controller
{
    protected $roleRepo;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepo = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->roleRepo->all();

        return success_response($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CreateRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoleRequest $request)
    {
        $result = $this->roleRepo->store($request->except('permissions'), $request['permissions']);

        return success_response($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->roleRepo->show($id);

        return success_response($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CreateRoleRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateRoleRequest $request, $id)
    {
        $this->roleRepo->update($id, $request->except('permissions'), $request['permissions']);

        return success_response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->roleRepo->deleteById($id);

        return success_response();
    }

    /**
     * get permission list
     *
     * @return \Illuminate\Http\Response
     */
    public function permissions()
    {
        $result = $this->roleRepo->permissions();

        return success_response($result);
    }
}
