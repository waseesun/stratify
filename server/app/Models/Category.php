<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;
    public const CREATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
    
    /**
     * The users that belong to the category.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_categories');
    }
}
