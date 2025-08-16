<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\RegisterProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Models\Problem;
use App\Models\Proposal;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }
    
    /**
     * @OA\Get(
     * path="/api/projects",
     * operationId="getProjects",
     * tags={"Projects"},
     * summary="Get all projects for the authenticated user",
     * description="Retrieves a paginated list of projects where the authenticated user is either the company or the provider. Admins see all projects.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Page number for pagination",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/ProjectPagination")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $query = Project::query();

            if (!$user->is_admin) {
                $userId = $user->id;

                // Filter projects where the authenticated user is either the company or the provider
                $query->whereHas('problem', function ($q) use ($userId) {
                    $q->where('company_id', $userId);
                })->orWhereHas('proposal', function ($q) use ($userId) {
                    $q->where('provider_id', $userId);
                });
            }

            $projects = $query->paginate(10);
            return response()->json($projects, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/projects/{project}",
     * operationId="getProjectById",
     * tags={"Projects"},
     * summary="Get a single project by ID",
     * description="Retrieves the details of a specific project. Accessible to the project's company, provider, or an admin.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="project",
     * in="path",
     * description="ID of the project to retrieve",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/ProjectShowResponse")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to view this project.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Project not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $project)
    {
        try {
            $project = Project::with([
                'problem.company:id,username',
                'proposal.provider:id,username'
            ])->find($project);

            if (!$project) {
                return response()->json([
                    "errors" => 'Project not found'
                ], 404);
            }

            if (
                $project->problem->company_id != Auth::user()->id &&
                $project->proposal->provider_id != Auth::user()->id &&
                !Auth::user()->is_admin
            ) {
                return response()->json([
                    "errors" => 'You are not authorized to view this project.'
                ], 403);
            }

            $projectArray = $project->toArray();

            if ($project->proposal) {
                $projectArray['proposal_title'] = $project->proposal->title;
                $projectArray['provider_name'] = $project->proposal->provider->username;
                unset($projectArray['proposal']);
            }

            if ($project->problem) {
                $projectArray['problem_title'] = $project->problem->title;
                $projectArray['company_name'] = $project->problem->company->username;
                unset($projectArray['problem']);
            }

            return response()->json($projectArray, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/projects",
     * operationId="createProject",
     * tags={"Projects"},
     * summary="Create a new project",
     * description="Creates a new project after a proposal has been accepted. The authenticated user must be the company owner of the problem or a super admin.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Project data to be stored",
     * @OA\JsonContent(
     * ref="#/components/schemas/RegisterProjectRequest",
     * example={
     * "problem_id": 1,
     * "proposal_id": 1,
     * "fee": 15000,
     * "start_date": "2025-08-15",
     * "end_date": "2025-11-15"
     * }
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Project created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Project created successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to create this project.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function create(RegisterProjectRequest $request)
    {
        try {
            $problem = Problem::find($request->problem_id);

            if (
                $problem->company_id != Auth::user()->id &&
                !Auth::user()->is_super_admin
            ) {
                return response()->json([
                    "errors" => 'You are not authorized to accept this project.'
                ], 403);
            }

            $validated = $request->validated();

            $project = Project::create($validated);

            $problem->status = 'sold';
            $problem->save();

            // reject other proposals
            $problem->proposals()
                ->where('id', '!=', $project->proposal_id)
                ->update(['status' => 'rejected']);

            $proposal = Proposal::find($request->proposal_id);
            $proposal->status = 'accepted';
            $proposal->save();

            return response()->json([
                "success" => 'Project created successfully'
            ], 201);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/projects/{project}",
     * operationId="updateProject",
     * tags={"Projects"},
     * summary="Update an existing project",
     * description="Updates the details of an existing project. Only the company that owns the project or a super admin can perform this action.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="project",
     * in="path",
     * description="ID of the project to update",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Project data to be updated",
     * @OA\JsonContent(
     * ref="#/components/schemas/RegisterProjectRequest",
     * example={
     * "fee": 20000,
     * "start_date": "2025-08-15",
     * "end_date": "2025-11-15",
     * "status": "completed"
     * }
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Project updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Project updated successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update this project.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Project not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateProjectRequest $request, string $project)
    {
        try {
            $project = Project::with([
                'problem.company:id',
                'proposal.provider:id'
            ])->find($project);

            if (!$project) {
                return response()->json([
                    "errors" => 'Project not found'
                ], 404);
            }

            if (
                $project->problem->company_id != Auth::user()->id &&
                !Auth::user()->is_super_admin
            ) {
                return response()->json([
                    "errors" => 'You are not authorized to update this project.'
                ], 403);
            }

            $validated = $request->validated();

            $project->update($validated);

            if (isset($validated['status']) && $validated['status'] == 'cancelled') {
                $problem = $project->problem;
                $problem->status = 'cancelled';
                $problem->save();

                $proposal = $project->proposal;
                $proposal->status = 'rejected';
                $proposal->save();
            }

            return response()->json([
                "success" => 'Project updated successfully'
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/projects/{project}",
     * operationId="deleteProject",
     * tags={"Projects"},
     * summary="Delete a project",
     * description="Deletes a project. Only the company that owns the project or a super admin can perform this action.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="project",
     * in="path",
     * description="ID of the project to delete",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="No Content: Project deleted successfully."
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete this project.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Project not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $project)
    {
        try {
            $project = Project::with('problem')->find($project);

            if (!$project) {
                return response()->json([
                    "errors" => 'Project not found'
                ], 404);
            }

            if (
                $project->problem->company_id != Auth::user()->id &&
                !Auth::user()->is_super_admin
            ) {
                return response()->json([
                    "errors" => 'You are not authorized to delete this project.'
                ], 403);
            }

            $project->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
