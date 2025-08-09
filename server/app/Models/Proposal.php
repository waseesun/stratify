<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'provider_id',
        'problem_id',
    ];
 
    /**
     * Get the user (provider) that owns the proposal.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Get the problem that the proposal belongs to.
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    /**
     * Get the docs for the proposal.
     */
    public function docs()
    {
        return $this->hasMany(ProposalDocs::class);
    }
    
    /**
     * Get the project associated with the problem.
     */
    public function project()
    {
        return $this->hasOne(Project::class);
    }
}
