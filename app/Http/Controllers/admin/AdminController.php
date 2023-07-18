<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CreateAdminRequest;
use App\Http\Requests\admin\UpdateAdminRequest;
use App\Models\RolePermission;
use App\Models\User;
use App\Repositories\admin\AdminRepository;
use App\Trait\AdminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    use AdminTrait;

    protected $adminRepo;

    public function __construct(AdminRepository $adminRepository)
    {
        $this->adminRepo = $adminRepository;
    }

    /**
     * login admin
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required',
            'remember' => 'required|boolean',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']], $request['remember'])) {
            $user = auth()->user();
            if (! $this->isAdmin()) {
                return response()->json(['status' => -1, 'message' => 'un authorized'], 401);
            }
            $user = User::with(['role'])->find($user->id);
            $token = $user->createToken('api_token')->plainTextToken;
            $permissions = RolePermission::with(['permission'])->where('role_id', $user->role_id)->get();
            $profile = get_profile($user);

            return response()->json(['status' => 1, 'message' => 'success', 'data' => ['token' => $token, 'remember_token' => $user->remember_token, 'user' => $profile, 'permissions' => $permissions]]);
        }

        return response()->json(['status' => 0, 'message' => __('auth.failed')], 500);
    }

    /**
     * login with remember token
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login_with_remember(Request $request)
    {
        $validator = Validator::make($request->all(), ['token' => 'required']);
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 500);
        }
        $user = User::where('remember_token', $request['token'])->first();
        if ($user == null) {
            return response()->json(['status' => 0, 'message' => 'auth failed'], 500);
        }
        $token = $user->createToken('api_token')->plainTextToken;
        $permissions = RolePermission::with(['permission'])->where('role_id', $user->role_id)->get();

        return response()->json(['status' => 1, 'message' => 'success', 'data' => ['token' => $token, 'remember_token' => $user->remember_token, 'user' => $user, 'permissions' => $permissions]]);
    }

    /**
     * get admins list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $result = $this->adminRepo->all();

        return success_response($result);
    }

    /**
     * get admin details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $result = $this->adminRepo->show($id);

        return success_response($result);
    }

    /**
     * create new admin
     *
     * @param  CreateAdminRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateAdminRequest $request)
    {
        $result = $this->adminRepo->create($request->all());

        return success_response($result);
    }

    /**
     * update admin
     *
     * @param  int  $id
     * @param  UpdateAdminRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAdminRequest $request)
    {
        $result = $this->adminRepo->update($request->all());

        return success_response();
    }

    /**
     * delete an admin
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->adminRepo->deleteById($id);

        return success_response();
    }
}
