<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'modules';
    protected $guarded = [];

    use NodeTrait;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test_cases()
    {
        return $this->hasMany(TestCase::class);
    }

}
