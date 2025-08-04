<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Http\Requests\Project\RegisterProblemRequest;
use App\Http\Requests\Project\UpdateProblemRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Http\OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="ProblemWithSkills",
 * title="Problem with Skills",
 * description="Problem model with associated skills",
 * allOf={
 * @OA\Schema(ref="#/components/schemas/Problem"),
 * @OA\Schema(
 * @OA\Property(
 * property="skillsets",
 * type="array",
 * @OA\Items(ref="#/components/schemas/ProblemSkillset"),
 * description="List of skills required for the problem."
 * )
 * )
 * }
 * )
 *
 * @OA\Schema(
 * schema="Problem",
 * title="Problem",
 * description="Problem model",
 * @OA\Property(property="id", type="integer", format="int64", description="Problem ID"),
 * @OA\Property(property="company_id", type="integer", format="int64", description="ID of the company that posted the problem"),
 * @OA\Property(property="category_id", type="integer", format="int64", description="ID of the problem's category"),
 * @OA\Property(property="title", type="string", description="Title of the problem"),
 * @OA\Property(property="description", type="string", nullable=true, description="Full description of the problem"),
 * @OA\Property(property="budget", type="integer", description="Budget for the problem"),
 * @OA\Property(property="timeline_value", type="integer", description="Numerical value for the timeline"),
 * @OA\Property(property="timeline_unit", type="string", enum={"day", "week", "month", "year"}, description="Unit for the timeline value"),
 * @OA\Property(property="status", type="string", enum={"open", "sold", "closed", "cancelled"}, description="Current status of the problem"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the problem was created"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the problem was last updated"),
 * example={
 * "id": 1,
 * "company_id": 1,
 * "category_id": 1,
 * "title": "Develop a Mobile E-commerce App",
 * "description": "We need an iOS and Android e-commerce application with payment gateway integration.",
 * "budget": 25000,
 * "timeline_value": 3,
 * "timeline_unit": "month",
 * "status": "open",
 * "created_at": "2023-07-25T10:00:00.000000Z",
 * "updated_at": "2023-07-25T10:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="ProblemSkillset",
 * title="ProblemSkillset",
 * description="Skill required for a problem",
 * @OA\Property(property="id", type="integer", format="int64", description="Skillset ID"),
 * @OA\Property(property="problem_id", type="integer", format="int64", description="ID of the associated problem"),
 * @OA\Property(property="skill", type="string", description="The required skill (e.g., 'React Native')"),
 * @OA\Property(property="created_at", type="string", format="date-time", nullable=true, description="Timestamp when the skill was added"),
 * @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the skill was last updated")
 * )
 */
class ProblemController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $problem)
    {
        try {
            //
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
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
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"company_id", "category_id", "title", "budget", "timeline_value", "timeline_unit", "skills"},
     * @OA\Property(property="company_id", type="integer", format="int64", description="ID of the company user creating the problem."),
     * @OA\Property(property="category_id", type="integer", format="int64", description="ID of the problem's category."),
     * @OA\Property(property="title", type="string", example="Develop a Mobile E-commerce App"),
     * @OA\Property(property="description", type="string", nullable=true, example="We need an iOS and Android e-commerce application with payment gateway integration."),
     * @OA\Property(property="budget", type="integer", example=25000, description="Budget for the problem in base currency units."),
     * @OA\Property(property="timeline_value", type="integer", example=3, description="Numerical value for the timeline (e.g., 3 for '3 months')."),
     * @OA\Property(property="timeline_unit", type="string", enum={"day", "week", "month", "year"}, example="month", description="Unit for the timeline value."),
     * @OA\Property(property="status", type="string", enum={"open", "sold", "closed", "cancelled"}, nullable=true, example="open", description="Current status of the problem (defaults to 'open')."),
     * @OA\Property(
     * property="skills",
     * type="array",
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
        try {
            if (Auth::user()->id !== $request->company_id && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to create a problem for this company ID."
                ], 403);
            }

            $problemData = $request->except('skills');
            $skillsData = $request->input('skills');

            $problem = Problem::create($problemData);

            $problemSkillsets = [];
            foreach ($skillsData as $skill) {
                $problemSkillsets[] = ['skill' => $skill];
            }

            if (!empty($problemSkillsets)) {
                $problem->skillsets()->createMany($problemSkillsets);
            }

            return response()->json([
                "success" => "Problem registered successfully."
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error registering problem: " . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
            return response()->json([
                "error" => "An unexpected error occurred: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProblemRequest $request, string $problem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $problem)
    {
        //
    }
}
