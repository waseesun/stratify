<?php

namespace App\Http\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Product Catalog API",
 * description="API documentation for the Product Catalog project",
 * @OA\Contact(
 * email="support@example.com"
 * )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer"
 * )
 *
 */
class Annotations
{
    // This class is just a container for the annotations. No actual code needed here.
}
