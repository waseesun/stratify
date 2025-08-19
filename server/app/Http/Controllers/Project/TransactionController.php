<?php

namespace App\Http\Controllers\Project;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\RegisterTransactionRequest;
use App\Models\Transaction;
use App\Models\Project;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }
    
    /**
     * @OA\Get(
     * path="/api/transactions",
     * operationId="getAllTransactions",
     * tags={"Transactions"},
     * summary="Get all transactions for the authenticated user",
     * description="Retrieves a paginated list of transactions. Access is limited to the company or provider involved, or to an admin.",
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
     * @OA\JsonContent(ref="#/components/schemas/TransactionPagination")
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
            $query = Transaction::query();
            
            if (!$user->isAdmin()) {
                if ($user->isCompany()) {
                    $query->where('company_id', $user->id);
                } elseif ($user->isProvider()) {
                    $query->where('provider_id', $user->id);
                }
            }

            $transactions = $query->paginate(10);

            return response()->json($transactions, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/transactions/{transaction}",
     * operationId="getTransactionById",
     * tags={"Transactions"},
     * summary="Get a single transaction by ID",
     * description="Retrieves the details of a specific transaction. Accessible to the transaction's company, provider, or an admin.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="transaction",
     * in="path",
     * description="ID of the transaction to retrieve",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(ref="#/components/schemas/Transaction")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to view this transaction.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Transaction not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $transaction)
    {
        try {
            $transaction = Transaction::find($transaction);

            if (!$transaction) {
                return response()->json([
                    "errors" => 'Transaction not found'
                ], 404);
            }

            if (
                !Auth::user()->isAdmin() &&
                ($transaction->company_id !== Auth::user()->id) &&
                ($transaction->provider_id !== Auth::user()->id)
            ) {
                return response()->json([
                    "errors" => 'You are not authorized to view this transaction'
                ], 403);
            }

            return response()->json($transaction, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/transactions",
     * operationId="createTransaction",
     * tags={"Transactions"},
     * summary="Create a new transaction",
     * description="Creates a new transaction for a project. Only the project's company or a super admin can perform this action.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Transaction data to be stored",
     * @OA\JsonContent(ref="#/components/schemas/RegisterTransactionRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Transaction created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Transaction created successfully")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to create a transaction for this project.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: The specified project was not found.",
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
    public function create(RegisterTransactionRequest $request)
    {
        try {
            $user = Auth::user();
            $validated = $request->validated();

            $project = Project::with('problem.company:id,username', 'proposal.provider:id,username')
                ->find($validated['project_id']);
                
            if ($user->id !== $project->problem->company_id && !$user->is_super_admin) {
                return response()->json([
                    "errors" => 'You are not authorized to create a transaction for this project.'
                ], 403);
            }

            $validated = $request->validated();

            $validated['company_id'] = $project->problem->company_id;
            $validated['provider_id'] = $project->proposal->provider_id;
            $validated['release_date'] = now()->toDateTimeString();

            Transaction::create($validated);

            Notification::create([
                'user_id' => $project->problem->company_id,
                'message' => $validated['amount'] . ' transfered to ' . $project->proposal->provider->username,
                'type' => 'transaction',
            ]);

            Notification::create([
                'user_id' => $project->proposal->provider_id,
                'message' => 'You have received ' . $validated['amount'] . ' from ' . $project->problem->company->username,
                'type' => 'transaction',
            ]);

            return response()->json([
                "success" => 'Transaction created successfully',
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
     * path="/api/transactions/{transaction}",
     * operationId="deleteTransaction",
     * tags={"Transactions"},
     * summary="Delete a transaction",
     * description="Deletes a transaction. This action is restricted to admins only.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="transaction",
     * in="path",
     * description="ID of the transaction to delete",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Transaction deleted successfully",
     * @OA\JsonContent(type="object", nullable=true)
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete this transaction.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Transaction not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $transaction)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json([
                "errors" => 'You are not authorized to delete this transaction'
            ], 403);
        }
        try {
            $transaction = Transaction::find($transaction);

            if (!$transaction) {
                return response()->json([
                    "errors" => 'Transaction not found'
                ], 404);
            }

            $transaction->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => 'An unexpected error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
