<?php

namespace App\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Шаблон модели
 * 
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class [ModelName] extends Model
{
    use HasFactory;
    // use SoftDeletes;  // Раскомментируйте для soft delete

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    // protected $table = 'custom_table_name';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        // Добавьте другие поля, которые можно массово заполнять
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        // 'password',
        // 'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            // 'is_active' => 'boolean',
            // 'metadata' => 'array',
            // 'published_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */

    // Один ко многим (hasMany)
    // public function posts()
    // {
    //     return $this->hasMany(Post::class);
    // }

    // Многие к одному (belongsTo)
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    // Многие ко многим (belongsToMany)
    // public function roles()
    // {
    //     return $this->belongsToMany(Role::class);
    // }

    // Один к одному (hasOne)
    // public function profile()
    // {
    //     return $this->hasOne(Profile::class);
    // }

    /**
     * Scopes
     */

    // public function scopeActive($query)
    // {
    //     return $query->where('is_active', true);
    // }

    // public function scopeRecent($query)
    // {
    //     return $query->orderBy('created_at', 'desc');
    // }

    /**
     * Accessors & Mutators
     */

    // Accessor (get)
    // protected function name(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn (string $value) => ucfirst($value),
    //     );
    // }

    // Mutator (set)
    // protected function name(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn (string $value) => strtolower($value),
    //     );
    // }
}
