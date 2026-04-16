<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Store;
use App\Models\roles;
use App\Models\orders;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory,HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'provider',
        'provider_id',
        'token',        
        'password',
        'last_country',
        'last_login',
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function store()
{
    return $this->hasOne(Store::class);
}
    // تشفير token عند التخزين
    public function setTokenAttribute($value)
    {
        $this->attributes['token'] = Crypt::encryptString($value);
    }
    // فك التشفير تلقائي عند الوصول للقيمة (اختياري)
    public function getTokenAttribute($value)
    {
        return Crypt::decryptString($value);
    }

    public function roles()
{
    return $this->belongsToMany(roles::class);
}
  public function orders()
    {
        return $this->hasMany(orders::class);
    }

}