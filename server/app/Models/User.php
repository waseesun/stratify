<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use \Illuminate\Validation\ValidationException;
use \Illuminate\Support\Facades\Hash;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasSlug, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'address',
        'role',
        'description',
        'image_url',
        'is_active',
        'is_admin',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Set the user's password with validation.
     */
    public function setPasswordAttribute($value)
    {
        if (! Hash::isHashed($value)) {
            $this->validatePasswordStrength($value);
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    /**
     * Validate password strength according to requirements.
     */
    protected function validatePasswordStrength($password)
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>[\]~\/\']/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages(['password' => $errors]);
        }
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions {
        return SlugOptions::create()
            ->generateSlugsFrom('email')
            ->saveSlugsTo('slug');
    }

    public function isCompany(): bool
    {
        return $this->role === 'company';
    }

    public function isProvider(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin;
    }

    /**
     * Get the problems posted by this user (if they are a company).
     */
    public function problems()
    {
        return $this->hasMany(Problem::class, 'company_id');
    }

    /**
     * Get the portfolio links for the user (if they are a provider).
     */
    public function portfolioLinks()
    {
        return $this->hasMany(PortfolioLink::class, 'provider_id');
    }

    /**
     * The categories that belong to the user.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'user_categories');
    }

    /**
     * Get the proposals made by this user (if they are a provider).
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'provider_id');
    }

    /**
     * Get the transactions made by this user as a provider.
     */
    public function providerTransactions()
    {
        return $this->hasMany(Transaction::class, 'provider_id');
    }

    /**
     * Get the transactions made by this user as a company.
     */
    public function companyTransactions()
    {
        return $this->hasMany(Transaction::class, 'company_id');
    }

    /**
     * Get the reviews this user has left.
     */
    public function reviewsGiven()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Get the reviews this user has received.
     */
    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Get the notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
