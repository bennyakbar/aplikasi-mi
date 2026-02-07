<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Student extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'nis',
        'name',
        'category_id',
        'grade',
        'academic_year',
        'status',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(StudentCategory::class, 'category_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
