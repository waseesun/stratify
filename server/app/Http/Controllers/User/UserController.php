<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\User\RegisterUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Traits\AuthenticateUser;
use App\OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 * schema="User",
 * title="User",
 * description="User model",
 * @OA\Property(property="id", type="integer", format="int64", description="User ID"),
 * @OA\Property(property="first_name", type="string", description="User's first name"),
 * @OA\Property(property="last_name", type="string", description="User's last name"),
 * @OA\Property(property="email", type="string", format="email", description="User's email address"),
 * @OA\Property(property="username", type="string", description="User's unique username"),
 * @OA\Property(property="address", type="string", nullable=true, description="User's address"),
 * @OA\Property(property="role", type="string", enum={"admin", "company", "provider"}, description="User's role"),
 * @OA\Property(property="is_admin", type="boolean", description="Indicates if the user has admin privileges"),
 * @OA\Property(property="is_active", type="boolean", description="Indicates if the user account is active"),
 * @OA\Property(property="description", type="string", nullable=true, description="User description (e.g., for providers)"),
 * @OA\Property(property="image_url", type="string", nullable=true, description="URL to user's profile image"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of user creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1, "first_name": "John", "last_name": "Doe", "email": "john.doe@example.com",
 * "username": "johndoe", "address": "123 Main St", "role": "company",
 * "is_admin": false, "is_active": true, "description": null, "image_url": null,
 * "created_at": "2023-01-01T12:00:00.000000Z", "updated_at": "2023-01-01T12:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="UserPagination",
 * title="User Pagination",
 * description="Paginated list of users",
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 * @OA\Property(property="links", type="object", description="Pagination links"),
 * @OA\Property(property="meta", type="object", description="Pagination meta information")
 * )
 */
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
     * @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="Number of items per page",
     * required=false,
     * @OA\Schema(type="integer", default=10)
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
     * description="Retrieves the details of a specific user by their ID or slug. A user can view their own profile, or an admin can view any user's profile.",
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
     * @OA\JsonContent(ref="#/components/schemas/User")
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

            if (Auth::user() !== $foundUser && !Auth::user()->isAdmin()) {
                return response()->json( [
                    "errors" => "You are not authorized to view this user."
                ], 403);
            }

            return response()->json($foundUser, 200);
        } catch (\Exception $e) {
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
                "success" => "User created successfully. Please verify your email to activate your account.",
            ], 201);
        } catch (\Exception $e) {
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
                "success" => "User created successfully. Please verify your email to activate your account.",
            ], 201);
        } catch (\Exception $e) {
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
                "success" => "User created successfully. Please verify your email to activate your account.",
            ], 201);
        } catch (\Exception $e) {
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
     * @OA\Property(property="user", ref="#/components/schemas/User")
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
                return response()->json( [
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
                "data" => $foundUser->fresh(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
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
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }
}