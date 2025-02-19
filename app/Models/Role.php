<?php

namespace App\Models;

use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * @method static RoleFactory factory(...$parameters)
 */
class Role extends SpatieRole
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }
} 