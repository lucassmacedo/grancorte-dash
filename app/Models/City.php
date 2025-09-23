<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    protected $fillable = ['code', 'name', 'uf'];

    public function scopeSearch($query, $search = null)
    {
        return $query->selectRaw("name || ' - ' || uf as name, code")
            ->when($search, function ($query, $search) {
                $query->whereRaw("lower(unaccent(name)) like lower(unaccent(?))", ["%{$search}%"]);
            })
            ->orderBy('name')
            ->get()
            ->pluck('name', 'code')
            ->map(function ($item, $key) {
                return [
                    'id'   => $key,
                    'text' => $item,
                ];
            });
    }
}
