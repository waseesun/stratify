<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\RegisterProposalRequest;
use App\Http\Requests\Project\UpdateProposalRequest;
use App\Models\Proposal;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ProposalController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * Handles the upload of proposal documents.
     * If $deleteOnly is true, it will delete all existing documents for the proposal.
     *
     * @param Proposal $proposal The proposal model instance.
     * @param array $files The array of uploaded files.
     * @param bool $deleteOnly Flag to indicate if only deletion should occur.
     * @return void
     */
    private function handleDocs(Proposal $proposal, array $files = [], bool $deleteOnly = false): void
    {
        // Handling deletion
        if ($deleteOnly) {
            $fileUrls = $proposal->docs()->pluck('file_url');

            foreach ($fileUrls as $fileUrl) {
                $this->deleteOldFile($fileUrl);
            }

            $proposal->docs()->delete();
            return;
        }

        // Upload files
        if (!empty($files)) {
            $proposalDocs = [];
            foreach ($files as $file) {
                $path = $file->store('proposal_docs', 'public');
                $proposalDocs[] = ['file_url' => Storage::url($path)];
            }
            
            $proposal->docs()->createMany($proposalDocs);
        }
    }

    /**
     * Deletes a file from public storage.
     *
     * @param string|null $fileUrl The URL of the file to delete.
     * @return void
     */
    private function deleteOldFile(?string $fileUrl): void
    {
        if ($fileUrl) {
            $path = str_replace(Storage::url(''), '', $fileUrl);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * @OA\Get(
     * path="/api/proposals",
     * operationId="getAllProposals",
     * tags={"Proposals"},
     * summary="Get all proposals",
     * description="Retrieves a paginated list of all proposals. Accessible to authenticated users.",
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
     * @OA\JsonContent(ref="#/components/schemas/ProposalPagination")
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
    public function index(): JsonResponse
    {
        try {
            $proposals = Proposal::paginate(10);
            return response()->json($proposals, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/proposals/{proposal}",
     * operationId="getProposalById",
     * tags={"Proposals"},
     * summary="Get a proposal by ID",
     * description="Retrieves the details of a specific proposal, including its associated documents. The user must be the provider who submitted the proposal, the company that posted the problem, or an admin.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="proposal",
     * in="path",
     * required=true,
     * description="ID of the proposal",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/ProposalWithDocs")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Proposal not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $proposal): JsonResponse
    {
        try {
            $proposal = Proposal::find($proposal);

            if (!$proposal) {
                return response()->json([
                    "errors" => 'Proposal not found'
                ], 404);
            }

            $proposal->load('docs');

            return response()->json($proposal, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/proposals",
     * operationId="createProposal",
     * tags={"Proposals"},
     * summary="Create a new proposal",
     * description="Allows a 'provider' user to submit a new proposal with associated PDF documents. Supports both JSON (without files) and multipart/form-data (with files).",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(property="provider_id", type="integer", description="ID of the provider submitting the proposal", example=1),
     * @OA\Property(property="problem_id", type="integer", description="ID of the problem the proposal is for", example=1),
     * @OA\Property(property="title", type="string", description="Title of the proposal", example="Innovative Solution for E-commerce"),
     * @OA\Property(property="description", type="string", description="Detailed description of the proposal", example="This proposal outlines a full-stack solution using React and Laravel."),
     * required={"provider_id", "problem_id", "title", "description"}
     * )
     * ),
     * @OA\MediaType(
    * mediaType="multipart/form-data",
    * @OA\Schema(
    * @OA\Property(property="provider_id", type="integer", description="ID of the provider submitting the proposal", example=1),
    * @OA\Property(property="problem_id", type="integer", description="ID of the problem the proposal is for", example=1),
    * @OA\Property(property="title", type="string", description="Title of the proposal", example="Innovative Solution for E-commerce"),
    * @OA\Property(property="description", type="string", description="Detailed description of the proposal", example="This proposal outlines a full-stack solution using React and Laravel."),
    * @OA\Property(
    * property="docs[]",
    * type="array",
    * @OA\Items(type="string", format="binary"),
    * description="An array of PDF files to be uploaded."
    * ),
    * required={"provider_id", "problem_id", "title", "description"}
    * )
    * )
     * ),
     * @OA\Response(
     * response=201,
     * description="Proposal created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Proposal created successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to create a proposal for this provider ID.",
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
    public function create(RegisterProposalRequest $request): JsonResponse
    {
        if (
        Auth::user()->role !== 'provider' &&
        Auth::user()->id !== $request->provider_id &&
        !Auth::user()->isSuperAdmin()
        ){
            return response()->json([
                "errors" => "You are not authorized to create a proposal for this provider ID."
            ], 403);
        }
        
        $validated = $request->validated();
        $proposalData = Arr::except($validated, ['docs']);
        $proposalDocsData = $validated['docs'];

        try {
            $proposal = Proposal::create($proposalData);
            
            $this->handleDocs($proposal, $proposalDocsData);

            return response()->json([
                "success" => "Proposal created successfully."
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
     * path="/api/proposals/{proposal}",
     * operationId="updateProposal",
     * tags={"Proposals"},
     * summary="Update an existing proposal",
     * description="Updates a proposal by ID. The user must be the provider or an admin. It supports both JSON (without files) and multipart/form-data (with files).",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="proposal",
     * in="path",
     * required=true,
     * description="ID of the proposal to update",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(property="title", type="string", description="Title of the proposal", nullable=true, example="Updated Title"),
     * @OA\Property(property="description", type="string", description="Detailed description of the proposal", nullable=true, example="Updated description with more details."),
     * )
     * ),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="title", type="string", description="Title of the proposal", nullable=true, example="Updated Title"),
     * @OA\Property(property="description", type="string", description="Detailed description of the proposal", nullable=true, example="Updated description with more details."),
     * @OA\Property(
     * property="docs[]",
     * type="array",
     * @OA\Items(type="string", format="binary"),
     * description="A new array of PDF files to be uploaded, which will replace all previous documents. This field is optional."
     * ),
     * @OA\Property(
     * property="_method",
     * type="string",
     * default="PATCH",
     * example="PATCH",
     * description="For form-data requests, this field is used to specify the HTTP method."
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Proposal updated successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update this proposal.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Proposal not found.",
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
    public function update(UpdateProposalRequest $request, string $proposal): JsonResponse
    {
        try {
            $proposal = Proposal::find($proposal);

            if (!$proposal) {
                return response()->json([
                    "errors" => "Proposal not found"
                ], 404);
            }

            if (Auth::user()->id !== $proposal->provider_id && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to update this proposal."
                ], 403);
            }
            
            $validated = $request->validated();
            $proposalData = Arr::except($validated, ['docs']);
            $proposalDocsData = Arr::get($validated, 'docs');

            $proposal->update($proposalData);

            if ($proposalDocsData !== null) {
                // Delete all previous documents
                $this->handleDocs($proposal, [], true); 
                // Upload and associate the new documents
                $this->handleDocs($proposal, $proposalDocsData);
            }

            return response()->json([
                "success" => "Proposal updated successfully."
            ], 200);

        } catch (\Exception $e) {
            Log::error("Error updating proposal: " . $e->getMessage(), ['exception' => $e, 'request_data' => $request->all()]);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/proposals/{proposal}",
     * operationId="deleteProposal",
     * tags={"Proposals"},
     * summary="Delete a proposal",
     * description="Deletes a proposal by ID. The user must be the provider or an admin. This will also delete all associated files from storage.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="proposal",
     * in="path",
     * required=true,
     * description="ID of the proposal to delete",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="No Content: Proposal deleted successfully."
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete this proposal.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Proposal not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $proposal): JsonResponse
    {
        try {
            $proposal = Proposal::find($proposal);

            if (!$proposal) {
                return response()->json([
                    "errors" => "Proposal not found"
                ], 404);
            }

            if (Auth::user()->id !== $proposal->provider_id && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to delete this proposal."
                ], 403);
            }
            
            // Delete the files
            $this->handleDocs($proposal, [], true);
            
            $proposal->docs()->delete();
            $proposal->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}