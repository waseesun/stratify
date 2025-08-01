<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;
    public const CREATED_AT = null;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the problems for the category.
     */
    public function problems()
    {
        return $this->hasMany(Problem::class);
    }
}
