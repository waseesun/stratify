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
    /**
     * Display a listing of the resource.
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
     * Display the specified resource.
     */
    public function show(string $project)
    {
        try {
            $project = Project::with([
                'problem.company:id,first_name,last_name',
                'proposal.provider:id,first_name,last_name'
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
                $projectArray['provider_name'] = $project->proposal->provider->first_name . ' ' . $project->proposal->provider->last_name;
                unset($projectArray['proposal']);
            }

            if ($project->problem) {
                $projectArray['company_name'] = $project->problem->company->first_name . ' ' . $project->problem->company->last_name;
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
     * Store a newly created resource in storage.
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
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, string $project)
    {
        try {
            $project = Project::with([
                'problem.company:id,first_name,last_name',
                'proposal.provider:id,first_name,last_name'
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $project)
    {
        try {
            $project = Project::find($project);

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
