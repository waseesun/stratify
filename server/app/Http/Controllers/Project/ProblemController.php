<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Http\Requests\Project\RegisterProblemRequest;
use App\Http\Requests\Project\UpdateProblemRequest;
use Illuminate\Support\Facades\Log;

class ProblemController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     try {
    //         $problems = Problem::paginate(10);
    //         return response()->json($problems, 200);
    //     } catch (\Exception $e) {
    //         Log::error($e);
    //         return response()->json([
    //             "errors" => $e->getMessage()
    //         ], 500);
    //     }
    // }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $problem)
    // {
    //     try {

    //     } catch (\Exception $e) {
    //         Log::error($e);
    //         return response()->json([
    //             "errors" => $e->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function create(RegisterProblemRequest $request)
    {
        
    }

//     /**
//      * Update the specified resource in storage.
//      */
//     public function update(UpdateProblemRequest $request, string $problem)
//     {
//         //
//     }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(string $problem)
//     {
//         //
//     }
}
