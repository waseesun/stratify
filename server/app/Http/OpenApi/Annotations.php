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
 * @OA\Tag(
 * name="Categories",
 * description="API Endpoints for Category Management"
 * )
 *
 * @OA\Tag(
 * name="Problems",
 * description="API Endpoints for Problem Management"
 * )
 * @OA\Tag(
 * name="Proposals",
 * description="API Endpoints for Proposal Management"
 * )
 * 
 * @OA\Tag(
 * name="Projects",
 * description="API Endpoints for Project Management"
 * )
 *
 * @OA\Schema(
 * schema="ErrorResponse",
 * title="Error Response",
 * description="Standard error response format for generic errors (e.g., 401, 403, 404, 500)",
 * @OA\Property(property="errors", type="string", description="Error message"),
 * example={"errors": "Something went wrong."}
 * )
 *
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
 * @OA\Property(property="current_page", type="integer", example=1),
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User")),
 * @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/users?page=1"),
 * @OA\Property(property="from", type="integer", example=1),
 * @OA\Property(property="last_page", type="integer", example=2),
 * @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/users?page=2"),
 * @OA\Property(
 * property="links",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="url", type="string", nullable=true, example="http://localhost:8000/api/users?page=1"),
 * @OA\Property(property="label", type="string", example="&laquo; Previous"),
 * @OA\Property(property="active", type="boolean", example=true)
 * )
 * ),
 * @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost:8000/api/users?page=2"),
 * @OA\Property(property="path", type="string", example="http://localhost:8000/api/users"),
 * @OA\Property(property="per_page", type="integer", example=10),
 * @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
 * @OA\Property(property="to", type="integer", example=10),
 * @OA\Property(property="total", type="integer", example=14)
 * )
 *
 * @OA\Schema(
 * schema="ProblemPagination",
 * title="Problem Pagination",
 * description="Paginated list of problems",
 * @OA\Property(property="current_page", type="integer", example=1),
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Problem")),
 * @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/problems?page=1"),
 * @OA\Property(property="from", type="integer", example=1),
 * @OA\Property(property="last_page", type="integer", example=2),
 * @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/problems?page=2"),
 * @OA\Property(
 * property="links",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="url", type="string", nullable=true, example="http://localhost:8000/api/problems?page=1"),
 * @OA\Property(property="label", type="string", example="&laquo; Previous"),
 * @OA\Property(property="active", type="boolean", example=true)
 * )
 * ),
 * @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost:8000/api/problems?page=2"),
 * @OA\Property(property="path", type="string", example="http://localhost:8000/api/problems"),
 * @OA\Property(property="per_page", type="integer", example=10),
 * @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
 * @OA\Property(property="to", type="integer", example=10),
 * @OA\Property(property="total", type="integer", example=14)
 * )
 *
 * @OA\Schema(
 * schema="PortfolioLink",
 * title="PortfolioLink",
 * description="Portfolio link model",
 * @OA\Property(property="id", type="integer", format="int64", description="ID of the portfolio link"),
 * @OA\Property(property="provider_id", type="integer", format="int64", description="ID of the user (provider) who owns this link"),
 * @OA\Property(property="link", type="string", format="url", description="The URL of the portfolio link"),
 * @OA\Property(property="created_at", type="string", format="date-time", nullable=true, description="Timestamp when the portfolio link was created"),
 * @OA\Property(property="updated_at", type="string", format="date-time", nullable=true, description="Timestamp when the portfolio link was last updated")
 * )
 *
 * @OA\Schema(
 * schema="Category",
 * title="Category",
 * description="Category model",
 * @OA\Property(property="id", type="integer", format="int64", description="Category ID"),
 * @OA\Property(property="name", type="string", description="Name of the category"),
 * example={
 * "id": 1,
 * "name": "Web Development"
 * }
 * )
 *
 * @OA\Schema(
 * schema="CategoryRequest",
 * title="Category Request",
 * description="Request body for creating or updating a category",
 * @OA\Property(property="name", type="string", description="Name of the category", example="Books"),
 * required={"name"}
 * )
 *
 * @OA\Schema(
 * schema="CategoryPivot",
 * title="CategoryPivot",
 * description="Pivot table attributes for the User-Category relationship",
 * @OA\Property(property="user_id", type="integer", description="ID of the user"),
 * @OA\Property(property="category_id", type="integer", description="ID of the category")
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
 * @OA\Property(property="status", type="string", enum={"open", "sold", "cancelled"}, description="Current status of the problem"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the problem was created"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the problem was last updated"),
 * example={
 * "id": 1, "company_id": 1, "category_id": 1, "title": "Develop a Mobile E-commerce App",
 * "description": "We need an iOS and Android e-commerce application with payment gateway integration.",
 * "budget": 25000, "timeline_value": 3, "timeline_unit": "month",
 * "status": "open", "created_at": "2023-07-25T10:00:00.000000Z",
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
 *
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
 * schema="ProposalDocs",
 * title="ProposalDocs",
 * description="Proposal document model",
 * @OA\Property(property="id", type="integer", format="int64", description="Document ID"),
 * @OA\Property(property="proposal_id", type="integer", format="int64", description="ID of the associated proposal"),
 * @OA\Property(property="file_url", type="string", format="url", description="URL to the uploaded PDF file"),
 * example={
 * "id": 1,
 * "proposal_id": 1,
 * "file_url": "http://localhost:8000/storage/proposal_docs/document.pdf"
 * }
 * )
 *
 * @OA\Schema(
 * schema="Proposal",
 * title="Proposal",
 * description="Proposal model",
 * @OA\Property(property="id", type="integer", format="int64", description="Proposal ID"),
 * @OA\Property(property="provider_id", type="integer", format="int64", description="ID of the provider who submitted the proposal"),
 * @OA\Property(property="problem_id", type="integer", format="int64", description="ID of the problem the proposal is for"),
 * @OA\Property(property="title", type="string", description="Title of the proposal"),
 * @OA\Property(property="description", type="string", description="Full description of the proposal"),
 * @OA\Property(property="status", type="string", enum={"submitted", "accepted", "rejected"}, description="Current status of the proposal"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp when the proposal was created"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp when the proposal was last updated"),
 * example={
 * "id": 1, "provider_id": 1, "problem_id": 1, "title": "My Solution to the Problem",
 * "description": "This is a detailed description of my proposed solution...",
 * "status": "submitted", "created_at": "2023-07-25T10:00:00.000000Z",
 * "updated_at": "2023-07-25T10:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="ProposalWithDocs",
 * title="Proposal with Documents",
 * description="Proposal model with associated documents",
 * allOf={
 * @OA\Schema(ref="#/components/schemas/Proposal"),
 * @OA\Schema(
 * @OA\Property(
 * property="docs",
 * type="array",
 * @OA\Items(ref="#/components/schemas/ProposalDocs"),
 * description="List of documents for the proposal."
 * )
 * )
 * }
 * )
 *
 * @OA\Schema(
 * schema="ProposalPagination",
 * title="Proposal Pagination",
 * description="Paginated list of proposals",
 * @OA\Property(property="current_page", type="integer", example=1),
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Proposal")),
 * @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/proposals?page=1"),
 * @OA\Property(property="from", type="integer", example=1),
 * @OA\Property(property="last_page", type="integer", example=2),
 * @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/proposals?page=2"),
 * @OA\Property(
 * property="links",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="url", type="string", nullable=true, example="http://localhost:8000/api/proposals?page=1"),
 * @OA\Property(property="label", type="string", example="&laquo; Previous"),
 * @OA\Property(property="active", type="boolean", example=true)
 * )
 * ),
 * @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost:8000/api/proposals?page=2"),
 * @OA\Property(property="path", type="string", example="http://localhost:8000/api/proposals"),
 * @OA\Property(property="per_page", type="integer", example=10),
 * @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
 * @OA\Property(property="to", type="integer", example=10),
 * @OA\Property(property="total", type="integer", example=14)
 * )
 *
 * @OA\Schema(
 * schema="Project",
 * title="Project",
 * description="Project model",
 * @OA\Property(property="id", type="integer", format="int64", description="Project ID"),
 * @OA\Property(property="problem_id", type="integer", format="int64", description="ID of the associated problem"),
 * @OA\Property(property="proposal_id", type="integer", format="int64", description="ID of the associated proposal"),
 * @OA\Property(property="fee", type="integer", description="Agreed-upon fee for the project"),
 * @OA\Property(property="status", type="string", enum={"in_progress", "completed", "cancelled"}, description="Current status of the project"),
 * @OA\Property(property="start_date", type="string", format="date", description="Project start date"),
 * @OA\Property(property="end_date", type="string", format="date", description="Project end date"),
 * @OA\Property(property="created_at", type="string", format="date-time", description="Timestamp of project creation"),
 * @OA\Property(property="updated_at", type="string", format="date-time", description="Timestamp of last update"),
 * example={
 * "id": 1, "problem_id": 1, "proposal_id": 1, "fee": 15000,
 * "status": "in_progress", "start_date": "2023-08-01", "end_date": "2023-11-01",
 * "created_at": "2023-08-01T12:00:00.000000Z", "updated_at": "2023-08-01T12:00:00.000000Z"
 * }
 * )
 *
 * @OA\Schema(
 * schema="ProjectPagination",
 * title="Project Pagination",
 * description="Paginated list of projects",
 * @OA\Property(property="current_page", type="integer", example=1),
 * @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Project")),
 * @OA\Property(property="first_page_url", type="string", example="http://localhost:8000/api/projects?page=1"),
 * @OA\Property(property="from", type="integer", example=1),
 * @OA\Property(property="last_page", type="integer", example=2),
 * @OA\Property(property="last_page_url", type="string", example="http://localhost:8000/api/projects?page=2"),
 * @OA\Property(
 * property="links",
 * type="array",
 * @OA\Items(
 * @OA\Property(property="url", type="string", nullable=true, example="http://localhost:8000/api/projects?page=1"),
 * @OA\Property(property="label", type="string", example="&laquo; Previous"),
 * @OA\Property(property="active", type="boolean", example=true)
 * )
 * ),
 * @OA\Property(property="next_page_url", type="string", nullable=true, example="http://localhost:8000/api/projects?page=2"),
 * @OA\Property(property="path", type="string", example="http://localhost:8000/api/projects"),
 * @OA\Property(property="per_page", type="integer", example=10),
 * @OA\Property(property="prev_page_url", type="string", nullable=true, example=null),
 * @OA\Property(property="to", type="integer", example=10),
 * @OA\Property(property="total", type="integer", example=14)
 * )
 *
 * @OA\Schema(
 * schema="ProjectShowResponse",
 * title="Project Show Response",
 * description="Detailed project response including company and provider names",
 * allOf={
 * @OA\Schema(ref="#/components/schemas/Project"),
 * @OA\Schema(
 * @OA\Property(property="provider_name", type="string", description="Username of the project's provider"),
 * @OA\Property(property="company_name", type="string", description="Username of the project's company")
 * )
 * },
 * example={
 * "id": 1, "problem_id": 1, "proposal_id": 1, "fee": 15000,
 * "status": "in_progress", "start_date": "2023-08-01", "end_date": "2023-11-01",
 * "created_at": "2023-08-01T12:00:00.000000Z", "updated_at": "2023-08-01T12:00:00.000000Z",
 * "provider_name": "provider1", "company_name": "company1"
 * }
 * )
 *
 * @OA\Schema(
 * schema="RegisterProjectRequest",
 * title="Register Project Request",
 * required={"problem_id", "proposal_id", "fee", "start_date", "end_date"},
 * @OA\Property(property="problem_id", type="integer", format="int64", description="ID of the problem to link to the project"),
 * @OA\Property(property="proposal_id", type="integer", format="int64", description="ID of the accepted proposal"),
 * @OA\Property(property="fee", type="integer", minLength=0, description="Agreed-upon fee for the project"),
 * @OA\Property(property="start_date", type="string", format="date", description="Project start date"),
 * @OA\Property(property="end_date", type="string", format="date", description="Project end date, must be after start_date")
 * )
 *
 * @OA\Schema(
 * schema="UpdateProjectRequest",
 * title="Update Project Request",
 * @OA\Property(property="fee", type="integer", minLength=0, description="Updated project fee", nullable=true),
 * @OA\Property(property="status", type="string", enum={"completed", "cancelled"}, description="Updated project status", nullable=true),
 * @OA\Property(property="start_date", type="string", format="date", description="Updated project start date", nullable=true),
 * @OA\Property(property="end_date", type="string", format="date", description="Updated project end date, must be after start_date", nullable=true)
 * )
 * 
 */
class Annotations
{
    // This class is just a container for the annotations. No actual code needed here.
}
