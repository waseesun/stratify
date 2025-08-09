<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'problem_id',
        'proposal_id',
        'fee',
        'status',
        'start_date',
        'end_date',
    ];

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
