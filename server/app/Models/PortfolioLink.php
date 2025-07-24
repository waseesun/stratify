<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioLink extends Model
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
        'provider_id',
        'link',
    ];

    /**
     * Get the user (provider) that owns the portfolio link.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}