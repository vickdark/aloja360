<?php

namespace App\Policies;

use App\Models\Quote;
use App\Models\Usuarios\Usuario;

class QuotePolicy
{
    public function viewAny(Usuario $user): bool
    {
        return $user->hasRole('admin') || ($user->role && $user->role->permissions()->where('slug', 'quotes.index')->exists());
    }

    public function view(Usuario $user, Quote $quote): bool
    {
        return $user->hasRole('admin') || ($user->role && $user->role->permissions()->where('slug', 'quotes.index')->exists());
    }

    public function create(Usuario $user): bool
    {
        return $user->hasRole('admin') || ($user->role && $user->role->permissions()->where('slug', 'quotes.create')->exists());
    }

    public function update(Usuario $user, Quote $quote): bool
    {
        return $user->hasRole('admin') || ($user->role && $user->role->permissions()->where('slug', 'quotes.edit')->exists());
    }

    public function delete(Usuario $user, Quote $quote): bool
    {
        return $user->hasRole('admin') || ($user->role && $user->role->permissions()->where('slug', 'quotes.destroy')->exists());
    }
}
