<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UpdatePortfolioLinksRequest;
use App\Http\Requests\User\UpdateUserCategoryRequest;
use App\Traits\AuthenticateUser;
use App\OpenApi\Annotations as OA;

class UserController extends Controller
{
    use AuthenticateUser;

    private function imageHandler(Request $request, array &$validated, ?User $user = null): void
    {
        if ($request->hasFile('image_url')) {
            if ($user && $user->image_url) {
                $this->deleteOldImage($user->image_url);
            }
            $path = $request->file('image_url')->store('profile_images', 'public');
            $validated['image_url'] = Storage::url($path);
        } else if (array_key_exists('image_url', $validated) && is_null($validated['image_url'])) {
            // If remove Image button is pressed we will send image_url null
            // otherwise the key won't be sent
            if ($user && $user->image_url) {
                $this->deleteOldImage($user->image_url);
            }
            $validated['image_url'] = null;
        } else {
            unset($validated['image_url']);
        }
    }

    private function deleteOldImage(?string $imageUrl): void
    {
        if ($imageUrl) {
            $path = str_replace(Storage::url(''), '', $imageUrl);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * @OA\Get(
     * path="/api/users",
     * operationId="getUsersList",
     * tags={"Users"},
     * summary="Get a paginated list of all users",
     * description="Retrieves a paginated list of all registered users. Requires admin privileges.",
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
     * @OA\JsonContent(ref="#/components/schemas/UserPagination")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to view all users.",
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
        $checkAuthUser = $this->ensureAuthenticated();

        if ($checkAuthUser) {
            return $checkAuthUser;
        }

        if (!Auth::user()->isAdmin()) {
            return response()->json([
                "errors" => "You are not authorized to view all users."
            ], 403);
        }

        try {
            $users = User::paginate(10);
            return response()->json($users, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/users/{user}",
     * operationId="getUserByIdOrSlug",
     * tags={"Users"},
     * summary="Get user details by ID or slug",
     * description="Retrieves the details of a specific user by their ID or slug. A user can view their own profile, or an admin can view any user's profile. Includes categories and portfolio links if applicable.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * description="ID or slug of the user to retrieve",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * allOf={
     * @OA\Schema(ref="#/components/schemas/User"),
     * @OA\Schema(
     * @OA\Property(
     * property="categories",
     * type="array",
     * @OA\Items(
     * allOf={
     * @OA\Schema(ref="#/components/schemas/Category"),
     * @OA\Schema(
     * @OA\Property(property="pivot", ref="#/components/schemas/CategoryPivot")
     * )
     * }
     * ),
     * nullable=true,
     * description="List of categories for the user."
     * )
     * ),
     * @OA\Schema(
     * @OA\Property(
     * property="portfolio_links",
     * type="array",
     * @OA\Items(ref="#/components/schemas/PortfolioLink"),
     * nullable=true,
     * description="List of portfolio links for 'provider' users."
     * )
     * )
     * }
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to view this user.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: User not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function show(string $user): JsonResponse
    {
        $checkAuthUser = $this->ensureAuthenticated();

        if ($checkAuthUser) {
            return $checkAuthUser;
        }

        try {
            $foundUser = User::where('id', $user)
                ->orWhere('slug', $user)
                ->first();

            if (!$foundUser) {
                return response()->json(['error' => 'User not found'], 404);
            }

            $foundUser->load('categories');

            if ($foundUser->role === 'provider') {
                $foundUser->load('portfolioLinks');
            }

            return response()->json($foundUser, 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/users/company",
     * operationId="createCompanyUser",
     * tags={"Users"},
     * summary="Create a new company user",
     * description="Registers a new user with the 'company' role. Sensitive fields like 'is_admin', 'is_active', and 'role' 
     * cannot be set by the client for this endpoint.",
     * @OA\RequestBody(
     * required=true,
     * description="User registration data. Can be JSON or Multipart Form Data.",
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"email", "username", "password", "password_confirmation"},
     * @OA\Property(property="first_name", type="string", nullable=true, example="Jane"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="Doe"),
     * @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com"),
     * @OA\Property(property="username", type="string", example="janedoe"),
     * @OA\Property(property="password", type="string", format="password", example="Password123!"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="Password123!"),
     * @OA\Property(property="address", type="string", nullable=true, example="456 Business Ave"),
     * @OA\Property(property="description", type="string", nullable=true, description="User's description (for provider roles, if applicable)."),
     * @OA\Property(property="image_url", type="string", nullable=true, description="URL to user's profile image."),
     * )
     * ),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"email", "username", "password", "password_confirmation"},
     * @OA\Property(property="first_name", type="string", nullable=true, example="Jane"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="Doe"),
     * @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com"),
     * @OA\Property(property="username", type="string", example="janedoe"),
     * @OA\Property(property="password", type="string", format="password", example="Password123!"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="Password123!"),
     * @OA\Property(property="address", type="string", nullable=true, example="456 Business Ave"),
     * @OA\Property(property="description", type="string", nullable=true, description="User's description (for provider roles, if applicable)."),
     * @OA\Property(property="image_url", type="string", format="binary", nullable=true, description="User's profile image file (e.g., .jpg, .png)."),
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="User created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Operation completed successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: Unauthorized key (e.g., is_admin, is_active, role) provided by client.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input for allowed fields.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object",
     * @OA\AdditionalProperties(
     * type="array",
     * @OA\Items(type="string", example="The field is required.")
     * )
     * ),
     * example={
     * "message": "The given data was invalid.",
     * "errors": {
     * "email": {"The email is already in use."},
     * "password": {"The password confirmation does not match."}
     * }
     * }
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function createCompanyUser(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['role'] = 'company';

        $this->imageHandler($request, $validated);

        try {
            User::create($validated);

            return response()->json([
                "success" => "User created successfully.",
            ], 201);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/users/provider",
     * operationId="createProviderUser",
     * tags={"Users"},
     * summary="Create a new provider user",
     * description="Registers a new user with the 'provider' role. Sensitive fields like 'is_admin', 'is_active', and 'role' 
     * cannot be set by the client for this endpoint.",
     * @OA\RequestBody(
     * required=true,
     * description="User registration data. Can be JSON or Multipart Form Data.",
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"email", "username", "password", "password_confirmation"},
     * @OA\Property(property="first_name", type="string", nullable=true, example="Provider"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="User"),
     * @OA\Property(property="email", type="string", format="email", example="provider@example.com"),
     * @OA\Property(property="username", type="string", example="provideruser"),
     * @OA\Property(property="password", type="string", format="password", example="Password123!"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="Password123!"),
     * @OA\Property(property="address", type="string", nullable=true, example="789 Service Rd"),
     * @OA\Property(property="description", type="string", nullable=true, example="Expert in IT services."),
     * @OA\Property(property="image_url", type="string", nullable=true, example="http://example.com/provider.jpg"),
     * )
     * ),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"email", "username", "password", "password_confirmation"},
     * @OA\Property(property="first_name", type="string", nullable=true, example="Provider"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="User"),
     * @OA\Property(property="email", type="string", format="email", example="provider@example.com"),
     * @OA\Property(property="username", type="string", example="provideruser"),
     * @OA\Property(property="password", type="string", format="password", example="Password123!"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="Password123!"),
     * @OA\Property(property="address", type="string", nullable=true, example="789 Service Rd"),
     * @OA\Property(property="description", type="string", nullable=true, example="Expert in IT services."),
     * @OA\Property(property="image_url", type="string", format="binary", nullable=true, description="User's profile image file (e.g., .jpg, .png)."),
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="User created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Operation completed successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: Unauthorized key (e.g., is_admin, is_active, role) provided by client.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input for allowed fields.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object",
     * @OA\AdditionalProperties(
     * type="array",
     * @OA\Items(type="string", example="The field is required.")
     * )
     * ),
     * example={
     * "message": "The given data was invalid.",
     * "errors": {
     * "email": {"The email is already in use."},
     * "password": {"The password confirmation does not match."}
     * }
     * }
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function createProviderUser(RegisterUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['role'] = 'provider';

        $this->imageHandler($request, $validated);

        try {
            User::create($validated);

            return response()->json([
                "success" => "User created successfully.",
            ], 201);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/users/admin",
     * operationId="createAdminUser",
     * tags={"Users"},
     * summary="Create a new admin user",
     * description="Registers a new user with the 'admin' role and sets 'is_admin' to true. Requires super super admin privileges for the authenticated user. 
     * Sensitive fields like 'is_active' and 'role' ('admin') cannot be set by the client for this endpoint.",
     * security={{"sanctum": {}}},
     * @OA\RequestBody(
     * required=true,
     * description="Admin user registration data. Can be JSON or Multipart Form Data.",
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * required={"email", "username", "password", "password_confirmation", "is_admin", "role"},
     * @OA\Property(property="first_name", type="string", nullable=true, example="Admin"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="User"),
     * @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     * @OA\Property(property="username", type="string", example="adminuser"),
     * @OA\Property(property="password", type="string", format="password", example="AdminPass123!"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="AdminPass123!"),
     * @OA\Property(property="address", type="string", nullable=true, example="100 Admin Rd"),
     * @OA\Property(property="description", type="string", nullable=true, description="User's description."),
     * @OA\Property(property="image_url", type="string", nullable=true, description="URL to user's profile image."),
     * )
     * ),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * required={"email", "username", "password", "password_confirmation", "is_admin", "role"},
     * @OA\Property(property="first_name", type="string", nullable=true, example="Admin"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="User"),
     * @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     * @OA\Property(property="username", type="string", example="adminuser"),
     * @OA\Property(property="password", type="string", format="password", example="AdminPass123!"),
     * @OA\Property(property="password_confirmation", type="string", format="password", example="AdminPass123!"),
     * @OA\Property(property="address", type="string", nullable=true, example="100 Admin Rd"),
     * @OA\Property(property="description", type="string", nullable=true, description="User's description."),
     * @OA\Property(property="image_url", type="string", format="binary", nullable=true, description="User's profile image file (e.g., .jpg, .png)."),
     * )
     * )
     * ),
     * @OA\Response(
     * response=201,
     * description="User created successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Operation completed successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: Unauthorized key (e.g., is_active, incorrect role) provided, or authenticated user lacks privileges to create an an admin.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input for allowed fields.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object",
     * @OA\AdditionalProperties(
     * type="array",
     * @OA\Items(type="string", example="The field is required.")
     * )
     * ),
     * example={
     * "message": "The given data was invalid.",
     * "errors": {
     * "email": {"The email is already in use."},
     * "password": {"The password confirmation does not match."}
     * }
     * }
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function createAdminUser(RegisterUserRequest $request): JsonResponse
    {
        $checkAuthUser = $this->ensureAuthenticated();

        if ($checkAuthUser) {
            return $checkAuthUser;
        }

        if (!Auth::user()->isSuperAdmin()) {
            return response()->json([
                "errors" => "You are not authorized to create an admin user."
            ], 403);
        }

        $validated = $request->validated();
        $validated['role'] = 'admin';
        $validated['is_admin'] = true;

        $this->imageHandler($request, $validated);

        try {
            User::create($validated);

            return response()->json([
                "success" => "User created successfully.",
            ], 201);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/users/{user}",
     * operationId="updateUser",
     * tags={"Users"},
     * summary="Update an existing user",
     * description="Updates the details of an existing user. A user can update their own profile, or a super admin can update any user's profile.
     * Sensitive fields like 'is_admin', 'is_active', and 'role' are ignored/discarded if sent in the request body, and 'password' is handled separately for updates.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * description="ID or slug of the user to update",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="User data to update. Can be JSON or Multipart Form Data.",
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(property="first_name", type="string", nullable=true, example="Updated Name"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="Updated Lastname"),
     * @OA\Property(property="username", type="string", nullable=true, example="updateduser"),
     * @OA\Property(property="address", type="string", nullable=true, example="789 New Address"),
     * @OA\Property(property="description", type="string", nullable=true, description="User's description."),
     * @OA\Property(property="image_url", type="string", nullable=true, description="Set to null to remove existing image."),
     * )
     * ),
     * @OA\MediaType(
     * mediaType="multipart/form-data",
     * @OA\Schema(
     * @OA\Property(property="first_name", type="string", nullable=true, example="Updated Name"),
     * @OA\Property(property="last_name", type="string", nullable=true, example="Updated Lastname"),
     * @OA\Property(property="username", type="string", nullable=true, example="updateduser"),
     * @OA\Property(property="address", type="string", nullable=true, example="789 New Address"),
     * @OA\Property(property="description", type="string", nullable=true, example="Updated description."),
     * @OA\Property(property="image_url", type="string", format="binary", nullable=true, description="User's profile image file (e.g., .jpg, .png). Send as empty string for removal."),
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="User updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="User updated successfully."),
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update this user.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: User not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input for allowed fields.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object",
     * @OA\AdditionalProperties(
     * type="array",
     * @OA\Items(type="string", example="The field is required.")
     * )
     * ),
     * example={
     * "message": "The given data was invalid.",
     * "errors": {
     * "email": {"The email is already in use."},
     * "username": {"The username is already taken by another user."}
     * }
     * }
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function update(UpdateUserRequest $request, string $user): JsonResponse
    {
        $checkAuthUser = $this->ensureAuthenticated();

        if ($checkAuthUser) {
            return $checkAuthUser;
        }

        try {
            $foundUser = User::where('id', $user)
                ->orWhere('slug', $user)
                ->first();

            if (!$foundUser) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }

            if (Auth::user() !== $foundUser && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to update this user."
                ], 403);
            }

            $validated = $request->validated();

            $this->imageHandler($request, $validated, $foundUser);

            // Handle password separately if it's being updated
            if (isset($validated['password'])) {
                $foundUser->password = $validated['password'];
                unset($validated['password']);
            }

            $foundUser->update($validated);

            return response()->json([
                "success" => "User updated successfully.",
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/users/{user}/portfolio-links",
     * operationId="updatePortfolioLinks",
     * tags={"Users"},
     * summary="Update a user's portfolio links",
     * description="Updates the portfolio links for a specific user. Only the user themselves or a super admin can update these links. The target user must have the 'provider' role.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * description="ID of the user whose portfolio links are to be updated.",
     * required=true,
     * @OA\Schema(type="integer", format="int64", example=1)
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Array of portfolio links (URLs) to associate with the user. Existing links not in this array will be deleted, and new ones will be added.",
     * @OA\MediaType(
     * mediaType="application/json",
     * @OA\Schema(
     * @OA\Property(
     * property="links",
     * type="array",
     * @OA\Items(type="string", format="url", example="https://example.com/my-portfolio/project1"),
     * description="An array of valid URLs representing the user's portfolio links."
     * ),
     * example={
     * "links": {
     * "https://example.com/portfolio-v1",
     * "https://another.site/my-work-v2"
     * }
     * }
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Portfolio links updated successfully",
     * @OA\JsonContent(
     * @OA\Property(property="success", type="string", example="Portfolio links updated successfully."),
     * )
     * ),
     * @OA\Response(
     * response=400,
     * description="Bad Request: Portfolio links can only be updated for users with the 'provider' role.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update portfolio links for this user.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: User not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error: Invalid input for portfolio links.",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(property="errors", type="object",
     * @OA\AdditionalProperties(
     * type="array",
     * @OA\Items(type="string", example="The links.0 field must be a valid URL.")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function updatePortfolioLinks(UpdatePortfolioLinksRequest $request, string $user): JsonResponse
    {
        $checkAuthUser = $this->ensureAuthenticated();

        if ($checkAuthUser) {
            return $checkAuthUser;
        }

        try {
            $foundUser = User::where('id', $user)
                            ->orWhere('slug', $user)
                            ->first();

            if (!$foundUser) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }

            if (
                (Auth::user() !== $foundUser && !Auth::user()->isSuperAdmin()) ||
                ($foundUser->role !== 'provider')
            ) {
                return response()->json([
                    "errors" => "You are not authorized to update portfolio links for this user."
                ], 403);
            }
            
            $validated = $request->validated();
            $newLinks = collect($validated['links'])->unique()->values();

            $existingLinks = $foundUser->portfolioLinks->pluck('link');
            $linksToDelete = $existingLinks->diff($newLinks);
            $linksToAdd = $newLinks->diff($existingLinks);

            if ($linksToDelete->isNotEmpty()) {
                $foundUser->portfolioLinks()->whereIn('link', $linksToDelete)->delete();
            }

            $portfolioLinksData = [];
            foreach ($linksToAdd as $link) {
                $portfolioLinksData[] = ['link' => $link];
            }

            if (!empty($portfolioLinksData)) {
                $foundUser->portfolioLinks()->createMany($portfolioLinksData);
            }

            return response()->json([
                "success" => "Portfolio links updated successfully.",
            ], 200);

        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "error" => "An unexpected error occurred: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     * path="/api/users/{user}/categories",
     * operationId="updateUserCategories",
     * tags={"Users"},
     * summary="Update a user's categories",
     * description="Synchronizes a user's categories with a new list. Old categories are removed, and new ones are added.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * description="ID or slug of the user to update",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="List of category IDs to associate with the user",
     * @OA\JsonContent(
     * required={"categories"},
     * @OA\Property(
     * property="categories",
     * type="array",
     * @OA\Items(type="integer"),
     * description="Array of category IDs",
     * example={1, 3, 5}
     * )
     * )
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     * @OA\Property(property="Success", type="string", example="Categories updated successfully.")
     * )
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to update categories for this user.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: User not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=422,
     * description="Validation Error",
     * @OA\JsonContent(
     * @OA\Property(property="message", type="string", example="The given data was invalid."),
     * @OA\Property(
     * property="errors",
     * type="object",
     * @OA\Property(
     * property="categories",
     * type="array",
     * @OA\Items(type="string", example="The categories field is required.")
     * )
     * )
     * )
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function updateUserCategory(UpdateUserCategoryRequest $request, string $user): JsonResponse
    {
         $checkAuthUser = $this->ensureAuthenticated();

        if ($checkAuthUser) {
            return $checkAuthUser;
        }

        try {
            $foundUser = User::where('id', $user)
                            ->orWhere('slug', $user)
                            ->first();

            if (!$foundUser) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }

            if (Auth::user() !== $foundUser && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    "errors" => "You are not authorized to update categories for this user."
                ], 403);
            }

            $categoryIds = $request->input('categories');

            $foundUser->categories()->sync($categoryIds);

            return response()->json([
                "Success" => "Categories updated successfully.",
            ], 200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "errors" => "An unexpected error occurred: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/users/{user}",
     * operationId="deleteUser",
     * tags={"Users"},
     * summary="Delete a user",
     * description="Deletes a user by their ID or slug. A user can delete their own account, or an admin can delete any user's account.",
     * security={{"sanctum": {}}},
     * @OA\Parameter(
     * name="user",
     * in="path",
     * description="ID or slug of the user to delete",
     * required=true,
     * @OA\Schema(type="string")
     * ),
     * @OA\Response(
     * response=204,
     * description="No Content: User deleted successfully."
     * ),
     * @OA\Response(
     * response=401,
     * description="Unauthenticated",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=403,
     * description="Forbidden: You are not authorized to delete this user.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=404,
     * description="Not Found: User not found.",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * ),
     * @OA\Response(
     * response=500,
     * description="Internal Server Error",
     * @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     * )
     * )
     */
    public function destroy(Request $request, string $user): JsonResponse
    {
        $checkAuthUser = $this->ensureAuthenticated();

        if ($checkAuthUser) {
            return $checkAuthUser;
        }

        try {
            $foundUser = User::where('id', $user)
                ->orWhere('slug', $user)
                ->first();

            if (!$foundUser) {
                return response()->json([
                    'error' => 'User not found'
                ], 404);
            }

            if (Auth::user() !== $foundUser && !Auth::user()->isSuperAdmin()) {
                return response()->json([
                    'errors' => 'You are not authorized to delete this user.'
                ], 403);
            }

            $this->deleteOldImage($foundUser->image_url);

            $foundUser->delete();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }
}