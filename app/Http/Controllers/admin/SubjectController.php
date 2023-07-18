<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\Subject\CreateSubjectRequest;
use App\Models\Subject;
use App\Repositories\admin\SubjectRepository;

class SubjectController extends Controller
{
    private $repo;

    /**
     * constructor
     *
     * @return void
     */
    public function __construct(SubjectRepository $repo)
    {
        $this->repo = $repo;
        $this->middleware('adminRole:view_subjects')->only(['index', 'show']);
        $this->middleware('adminRole:create_subjects')->only('create');
        $this->middleware('adminRole:update_subjects')->only('update');
        $this->middleware('adminRole:delete_subjects')->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjects = Subject::with(['specialty'])->get();

        return success_response($subjects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSubjectRequest $request)
    {
        $result = $this->repo->create($request->all());

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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreateSubjectRequest $request, $id)
    {
        $result = $this->repo->updateById($id, $request->all());

        return success_response($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
