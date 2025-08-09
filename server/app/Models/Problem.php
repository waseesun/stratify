<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'category_id',
        'title',
        'description',
        'budget',
        'timeline_value',
        'timeline_unit',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'timeline_unit' => 'integer',
        'budget' => 'integer',
    ];

    /**
     * Get the company that owns the problem.
     */
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    /**
     * Get the category that the problem belongs to.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the skillsets for the problem.
     */
    public function skillsets()
    {
        return $this->hasMany(ProblemSkillset::class);
    }

    /**
     * Get the proposals for the problem.
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Get the project associated with the problem.
     */
    public function project()
    {
        return $this->hasOne(Project::class);
    }
}