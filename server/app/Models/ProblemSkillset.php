<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProblemSkillset extends Model
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
        'problem_id',
        'skill',
    ];

    /**
     * Get the problem that the skill belongs to.
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}