<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use hasFactory;

    protected $fillable = [
        'project_id',
        'provider_id',
        'company_id',
        'milestone_name',
        'amount',
        'release_date',
    ];

    protected $casts = [
        'amount' => 'integer',
        'release_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }
}
