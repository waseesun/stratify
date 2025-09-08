<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Problem;
use App\Models\Notification;
use App\Http\Requests\Project\RegisterProblemRequest;
use App\Http\Requests\Project\UpdateProblemRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\OpenApi\Annotations as OA;

class ProblemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     * path="/api/problems",
     * operationId="getAllProblems",
     * tags={"Problems"},
     * summary="Get all problems with optional filters",
     * description="Retrieves a paginated list of problems. You can optionally filter by title and status. This endpoint is accessible to authenticated users.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="page",
     * in="query",
     * description="Page number for pagination",
     * required=false,
     * @OA\Schema(type="integer", default=1)
     * ),
     * @OA\Parameter(
     * name="title",
     * in="query",
     * description="Filter problems by title (case-insensitive, partial match)",
     * required=false,
     * @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     * name="status",
     * in="query",
     * description="Filter problems by status (e.g., 'open', 'sold', 'cancelled')",
     * required=false,
     * @OA\Schema(type="string", enum={"open", "sold", "cancelled"})
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/ProblemPagination")
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
    public function index(Request $request)
    {
        try {
            $title = $request->query('title');
            $status = $request->query('status');

            $query = Problem::query();

            if ($title) {
                $query->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($title) . '%']);
            }

            if ($status) {
                $query->where('status', $status);
            }

            $problems = $query->paginate(10);
            return response()->json($problems, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function allIndex(Request $request)
    {
        try {
            $problems = Problem::all();
            return response()->json($problems, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/problems/company",
     * operationId="getCompanyProblems",
     * tags={"Problems"},
     * summary="Get all problems for a company",
     * description="Retrieves a paginated list of all problems for a company. This endpoint is accessible to authenticated company users.",
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
     * @OA\JsonContent(ref="#/components/schemas/ProblemPagination")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to view this company's problems. (e.g., not a company user).",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function companyIndex()
    {
        if (Auth::user()->role !== 'company') {
            return response()->json([
                "errors" => 'You are not authorized to view this company\'s problems.'
            ], 403);
        }

        try {
            $user = Auth::user();
            $problems = Problem::where('company_id', $user->id)->paginate(10);
            return response()->json($problems, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/problems/{problem}",
     * operationId="getProblemById",
     * tags={"Problems"},
     * summary="Get a single problem by ID",
     * description="Retrieves the details of a specific problem, including its skills.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="problem",
     * in="path",
     * description="ID of the problem to retrieve",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/ProblemWithSkills")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Problem not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $problem)
    {
        try {
            $user = Auth::user();

            $problem = Problem::with('skillsets', 'company:id,username')
                ->find($problem);

            if (!$problem) {
                return response()->json([
                    "errors" => "Problem not found"
                ], 404);
            }

            if (
                $user->isAdmin() ||
                ($user->isCompany() && $problem->company_id == $user->id)
            ) {
                $problem->load([
                    'proposals' => function ($q) {
                        $q->select('id', 'problem_id', 'provider_id', 'title', 'status')
                            ->with('provider:id,username');
                    }
                ]);
            } elseif ($user->isProvider()) {
                $problem->load([
                    'proposals' => function ($q) use ($user) {
                        $q->where('provider_id', $user->id)
                            ->select('id', 'problem_id', 'provider_id', 'title', 'status')
                            ->with('provider:id,username');
                    }
                ]);
            }

            $problemArray = $problem->toArray();
            if ($problem->company) {
                $problemArray['company_name'] = $problem->company->username;
                unset($problemArray['company']); //Removing company object
            }

            return response()->json($problemArray, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/problems",
     * operationId="registerProblem",
     * tags={"Problems"},
     * summary="Register a new problem with associated skills",
     * description="Creates a new problem entry, including its title, description, budget, timeline, and a list of required skills. The authenticated user must be a 'company' role.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Problem data including associated skills.",
     * @OA\JsonContent(
     * required={"company_id", "category_id", "title", "budget", "timeline_value", "timeline_unit", "skills"},
     * @OA\Property(property="company_id", type="integer", format="int64", description="ID of the company user creating the problem."),
     * @OA\Property(property="category_id", type="integer", format="int64", description="ID of the problem's category."),
     * @OA\Property(property="title", type="string", example="Develop a Mobile E-commerce App"),
     * @OA\Property(property="description", type="string", nullable=true, example="We need an iOS and Android e-commerce application with payment gateway integration."),
     * @OA\Property(property="budget", type="integer", example=25000, description="Budget for the problem in base currency units."),
     * @OA\Property(property="timeline_value", type="integer", example=3, description="Numerical value for the timeline (e.g., 3 for '3 months')."),
     * @OA\Property(property="timeline_unit", type="string", enum={"day", "week", "month", "year"}, example="month", description="Unit for the timeline value."),
     * @OA\Property(
     * property="skills",
     * type="array",
     * minItems=1,
     * @OA\Items(type="string", example="React Native"),
     * description="Array of required skills for the problem."
     * ),
     * example={
     * "company_id": 1,
     * "category_id": 1,
     * "title": "Develop a Mobile E-commerce App",
     * "description": "We need an iOS and Android e-commerce application with payment gateway integration.",
     * "budget": 25000,
     * "timeline_value": 3,
     * "timeline_unit": "month",
     * "skills": {"React Native", "Node.js", "MongoDB", "UI/UX Design"}
     * }
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Problem registered successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Problem registered successfully."),
     * @OA\Property(
     * property="data",
     * ref="#/components/schemas/ProblemWithSkills"
     * )
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to register a problem (e.g., not a company user).",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input for problem or skills.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function create(RegisterProblemRequest $request): JsonResponse
    {
        if (Auth::user()->id !== (int) $request->company_id && !Auth::user()->isSuperAdmin()) {
            return response()->json([
                "errors" => "You are not authorized to create a problem for this company ID."
            ], 403);
        }

        $validated = $request->validated();
        $problemData = Arr::except($validated, ['skills']);
        $skillsData = $validated['skills'];

        try {
            $problem = Problem::create($problemData);

            $problemSkillsets = [];
            foreach ($skillsData as $skill) {
                $problemSkillsets[] = ['skill' => $skill];
            }

            if (!empty($problemSkillsets)) {
                $problem->skillsets()->createMany($problemSkillsets);
            }

            // Notification
            Notification::create([
                'user_id' => Auth::user()->id,
                'message' => $problem->title . ' has been registered for you.',
                'type' => 'problem',
                'link' => '/problem/' . $problem->id
            ]);

            $problemCategoryId = $problem->category_id;
            $providers = User::where('role', 'provider')
                ->whereHas('categories', function ($query) use ($problemCategoryId) {
                    $query->where('categories.id', $problemCategoryId);
                })->get();

            foreach ($providers as $provider) {
                Notification::create([
                    'user_id' => $provider->id,
                    'message' => 'A new problem has been posted: ' . $problem->title,
                    'type' => 'problem',
                    'link' => '/problem/' . $problem->id
                ]);
            }

            return response()->json([
                "success" => "Problem registered successfully."
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
     * path="/api/problems/{problem}",
     * operationId="updateProblem",
     * tags={"Problems"},
     * summary="Update an existing problem",
     * description="Updates the details of an existing problem and its associated skills. Only the company that owns the problem or a super admin can perform this action.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="problem",
     * in="path",
     * description="ID of the problem to update",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Problem data to update, including skills. All fields are optional except for skills, which should be provided as a complete list to synchronize.",
     * @OA\JsonContent(
     * @OA\Property(property="category_id", type="integer", format="int64", description="ID of the problem's category.", nullable=true),
     * @OA\Property(property="title", type="string", example="Updated Mobile App Project", nullable=true),
     * @OA\Property(property="description", type="string", example="New description for the project.", nullable=true),
     * @OA\Property(property="budget", type="integer", example=30000, description="Updated budget.", nullable=true),
     * @OA\Property(property="timeline_value", type="integer", example=4, description="Updated timeline value.", nullable=true),
     * @OA\Property(property="timeline_unit", type="string", enum={"day", "week", "month", "year"}, example="month", description="Updated timeline unit.", nullable=true),
     * @OA\Property(property="status", type="string", enum={"open", "cancelled"}, example="open", description="Updated problem status.", nullable=true),
     * @OA\Property(
     * property="skills",
     * type="array",
     * minItems=1,
     * @OA\Items(type="string", example="Angular"),
     * description="Array of skills to synchronize. This will replace the old skill list.",
     * nullable=true
     * ),
     * example={
     * "title": "Updated Mobile App Project",
     * "budget": 30000,
     * "skills": {"Angular", "Node.js", "Express.js"}
     * }
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Problem updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Problem updated successfully."),
     * @OA\Property(property="data", ref="#/components/schemas/ProblemWithSkills")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update this problem.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Problem not found.",
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
    public function update(UpdateProblemRequest $request, string $problem)
    {
        try {
            $problem = Problem::find($problem);

            if (!$problem) {
                return response()->json([
                    "errors" => "Problem not found"
                ], 404);
            }

            if (Auth::user()->id !== $problem->company_id && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to update a problem for this company ID."
                ], 403);
            }

            $validated = $request->validated();
            $problemData = Arr::except($validated, ['skills']);

            $skillsData = null;
            if (isset($validated['skills'])) {
                $skillsData = $validated['skills'];
            }

            $problem->update($problemData);

            if ($skillsData !== null) {
                $existingSkillNames = $problem->skillsets()->pluck('skill')->toArray();

                $skillsToAdd = array_diff($skillsData, $existingSkillNames);
                $skillsToRemove = array_diff($existingSkillNames, $skillsData);

                if (!empty($skillsToRemove)) {
                    $problem->skillsets()->whereIn('skill', $skillsToRemove)->delete();
                }

                if (!empty($skillsToAdd)) {
                    $problem->skillsets()->createMany(
                        collect($skillsToAdd)->map(fn($skill) => ['skill' => $skill])->toArray()
                    );
                }
            }

            return response()->json([
                "success" => "Problem updated successfully."
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error updating problem: " . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
            return response()->json([
                "error" => "An unexpected error occurred: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/problems/{problem}",
     * operationId="deleteProblem",
     * tags={"Problems"},
     * summary="Delete a problem",
     * description="Deletes a problem and its associated skills. Only the company that owns the problem or a super admin can perform this action.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="problem",
     * in="path",
     * description="ID of the problem to delete",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Problem deleted successfully (No Content)."
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete this problem.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Problem not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $problem)
    {
        try {
            $problem = Problem::find($problem);

            if (!$problem) {
                return response()->json([
                    "errors" => "Problem not found"
                ], 404);
            }

            if (Auth::user()->id !== $problem->company_id && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to delete a problem for this company ID."
                ], 403);
            }

            $problem->skillsets()->delete();
            $problem->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
