<?php

namespace App\Http\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Stratify API",
 * description="API documentation for the Stratify project",
 * @OA\Contact(
 * email="support@example.com"
 * )
 * )
 *
 * @OA\SecurityScheme(
 * securityScheme="sanctum",
 * type="http",
 * scheme="bearer"
 * )
 *
 * @OA\Tag(
 * name="Users",
 * description="API Endpoints for User Management"
 * )
 *
 * @OA\Schema(
 * schema="ErrorResponse",
 * title="Error Response",
 * description="Standard error response format for generic errors (e.g., 401, 403, 404, 500)",
 * @OA\Property(property="errors", type="string", description="Error message"),
 * example={"errors": "Something went wrong."}
 * )
 */
class Annotations
{
    // This class is just a container for the annotations. No actual code needed here.
}
