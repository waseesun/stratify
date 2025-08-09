<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use hasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_id',
        'provider_id',
        'company_id',
        'milestone_name',
        'amount',
        'release_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'amount' => 'integer',
        'release_date' => 'date',
    ];

    /**
     * Get the project that owns the transaction.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user (provider) that owns the transaction.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Get the user (company) that owns the transaction.
     */
    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
}
