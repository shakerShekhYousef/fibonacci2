<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('adminRole:view_users')->only(['index', 'show']);
    }

    /**
     * get users list
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }
}
