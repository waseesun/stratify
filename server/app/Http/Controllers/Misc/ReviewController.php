<?php

namespace App\Http\Controllers\Misc;

use App\Http\Controllers\Controller;
use App\Http\Requests\Misc\RegisterReviewRequest;
use App\Http\Requests\Misc\UpdateReviewRequest;
use App\Models\Review;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct() {
        $this->middleware('auth:sanctum');
    }

    /**
     * @OA\Get(
     * path="/api/reviews/{user}",
     * operationId="getReviewsForUser",
     * tags={"Reviews"},
     * summary="Get reviews for a specific user",
     * description="Retrieves a paginated list of reviews for a user by their ID. This endpoint is publicly accessible.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * description="ID of the user to get reviews for",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
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
     * @OA\JsonContent(ref="#/components/schemas/ReviewPagination")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function index(string $user)
    {
        try {
            $review = Review::where('reviewee_id', $user)->get();
            return response()->json($review);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/reviews",
     * operationId="createReview",
     * tags={"Reviews"},
     * summary="Create a new review",
     * description="Creates a new review for a user. The logged-in user's ID is used as the reviewer.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Review data to be stored",
     * @OA\JsonContent(ref="#/components/schemas/RegisterReviewRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Review created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Your review for 123 has been saved.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: Cannot review yourself.",
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
    public function create(RegisterReviewRequest $request)
    {
        try {
            $validated = $request->validated();

            if (Auth::user()->id == $validated['reviewee_id']) {
                return response()->json([
                    'error' => 'You cannot review yourself'
                ], 403);
            }

            $validated['reviewer_id'] = Auth::user()->id;

            Review::create($validated);

            Notification::create([
                'user_id' => $validated['reviewee_id'],
                'notification' => 'You have a new review from ' . Auth::user()->name,
                'type' => 'review',
            ]);

            return response()->json([
                'success' => 'Your review for ' . $validated['reviewee_id'] . ' has been saved.'
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/reviews/{review}",
     * operationId="updateReview",
     * tags={"Reviews"},
     * summary="Update an existing review",
     * description="Updates a review. Only the user who created the review can update it.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="review",
     * in="path",
     * description="ID of the review to update",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Review data to be updated",
     * @OA\JsonContent(ref="#/components/schemas/UpdateReviewRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Review updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Your review for 123 has been updated.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update this review.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Review not found.",
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
    public function update(UpdateReviewRequest $request, string $review)
    {
        try {
            $review = Review::find($review);

            if (!$review) {
                return response()->json([
                    'error' => 'Review not found'
                ], 404);
            }

            if (Auth::user()->id !== $review->reviewer_id) {
                return response()->json([
                    'error' => 'You are not authorized to update this review'
                ], 403);
            }

            $validated = $request->validated();

            $review->update($validated);

            return response()->json([
                'success' => 'Your review for ' . $review->reviewee_id . ' has been updated.'
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/reviews/{review}",
     * operationId="deleteReview",
     * tags={"Reviews"},
     * summary="Delete a review",
     * description="Deletes a review. This action is restricted to the original reviewer or an admin.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="review",
     * in="path",
     * description="ID of the review to delete",
     * required=true,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=204,
     * description="Review deleted successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Your review for 123 has been deleted.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete this review.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: Review not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $review)
    {
        try {
            $review = Review::find($review);

            if (!$review) {
                return response()->json([
                    'error' => 'Review not found'
                ], 404);
            }

            if (
                !Auth::user()->is_admin &&
                Auth::user()->id !== $review->reviewer_id
            ) {
                return response()->json([
                    'error' => 'You are not authorized to delete this review'
                ], 403);
            }

            $review->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
