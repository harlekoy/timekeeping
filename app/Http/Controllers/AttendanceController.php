<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\PostResource;
use App\Models\Attendance;
use App\Traits\ApiResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    use ApiResource;

    /**
     * Initialize API resource.
     *
     * @return array
     */
    public function init()
    {
        return [
            'model'    => Attendance::class,
            'request'  => Request::class,
            'resource' => AttendanceResource::class,
        ];
    }

    /**
     * Fetching controller event.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\QueryBuilder &$builder
     *
     * @return void
     */
    public function fetching($request, &$model)
    {
        $model->where('user_id', Auth::id())->latest();
    }

    /**
     * Creating controller event.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\QueryBuilder &$builder
     *
     * @return void
     */
    public function creating($request, &$model)
    {
        $model->forceFill([
            'user_id' => Auth::id(),
        ]);
    }
}
