<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\CourseController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\StudentController as AdminStudentController;
use App\Http\Controllers\admin\SubjectController;
use App\Http\Controllers\admin\teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\admin\teacher\VideoController as TeacherVideoController;
use App\Http\Controllers\admin\TeacherController as AdminTeacherController;
use App\Http\Controllers\admin\VideoController;
use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\CourseController as ApiCourseController;
use App\Http\Controllers\api\QuestionController;
use App\Http\Controllers\api\QuizController;
use App\Http\Controllers\api\SpecialtyController;
use App\Http\Controllers\api\StudentController;
use App\Http\Controllers\api\SubjectController as ApiSubjectController;
use App\Http\Controllers\api\TeacherController;
use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('login_remember', [AuthController::class, 'login_remember']);
Route::post('password/send_code', [AuthController::class, 'send_code']);
Route::post('password/confirm_code', [AuthController::class, 'confirm']);
Route::post('password/reset', [AuthController::class, 'reset_password']);
Route::post('test/upload', [ApiCourseController::class, 'uploadVideo']);
//get specialties list
Route::get('/specialties', SpecialtyController::class);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'update']);
    Route::post('/profile', [UserController::class, 'update_image']);
    Route::get('subjects', ApiSubjectController::class);
    Route::get('courses', ApiCourseController::class);
});
Route::prefix('teacher')->group(function () {
    Route::group(['middleware' => ['auth:sanctum', 'checkRole'], 'roles' => ['Teacher', 'Admin', 'Student']], function () {
        Route::get('get_my_courses', [TeacherController::class, 'get_my_courses']);
        Route::post('add_quiz_to_video', [QuizController::class, 'add_quiz_to_video']);
        Route::apiResource('quizzes', QuizController::class);
        Route::apiResource('questions', QuestionController::class);
    });
});
Route::prefix('student')->group(function () {
    Route::group(['middleware' => ['auth:sanctum', 'checkRole'], 'roles' => ['Student']], function () {
        Route::post('save_video_progress', [StudentController::class, 'save_video_progress']);
        Route::get('get_course_videos_progress', [ApiCourseController::class, 'get_course_videos_progress']);
        Route::post('quiz/take', [QuestionController::class, 'answerQuestion']);
        Route::get('quizes', [QuizController::class, 'student_quizes']);
        Route::post('buy_video',[StudentController::class,'buy_video']);
        Route::get('get_courses_subscribed',[ApiCourseController::class,'get_courses_subscribed']);
    });
});
Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('/login', [AdminController::class, 'login']);
        Route::post('/login/token', [AdminController::class, 'login_with_remember']);
    });
    Route::middleware('auth:sanctum')->group(function () {
        Route::middleware('canAddVideos')->group(function () {
            Route::get('teacher/video/{id}', [TeacherVideoController::class, 'index']);
            Route::post('teacher/video/{id}', [TeacherVideoController::class, 'update']);
            Route::apiResource('teacher/video', TeacherVideoController::class);
            Route::get('teacher/courses', TeacherCourseController::class);
        });
        Route::post('courses/{id}', [CourseController::class, 'update']);
        Route::apiResource('courses', CourseController::class);
        Route::post('courses/freeTrail/{id}', [CourseController::class, 'setFreeTrail']);
        Route::apiResource('subjects', SubjectController::class);
        Route::post('videos/{id}', [VideoController::class, 'update']);
        Route::apiResource('videos', VideoController::class);
        Route::get('teachers', [AdminTeacherController::class, 'index']);
        Route::post('teachers/delete', [AdminTeacherController::class, 'delete']);

        Route::post('teachers/give_permission/{id}', [AdminTeacherController::class, 'give_permission']);
        Route::delete('teacher/remove_permission/{id}', [AdminTeacherController::class, 'remove_permission']);

        Route::get('students', [AdminStudentController::class, 'index']);
        Route::post('students/balance', [AdminStudentController::class, 'change_balance']);

        Route::apiResource('admins', AdminController::class);

        Route::apiResource('roles', RoleController::class);

        Route::get('permissions', [RoleController::class, 'permissions']);
    });
});
