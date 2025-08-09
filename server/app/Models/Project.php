<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'problem_id',
        'proposal_id',
        'fee',
        'status',
        'start_date',
        'end_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'fee' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the problem that owns the project.
     */
    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }

    /**
     * Get the proposal that owns the project.
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    /**
     * Get the transactions for the project.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
