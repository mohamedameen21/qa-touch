<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestCase extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'test_cases';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // Before creating the model
        static::creating(function ($testCase) {
            $testCase->ticket_no = $testCase->generateTicketNumber();
        });
    }

    private function generateTicketNumber()
    {
        $lastTestCase = static::orderBy('id', 'desc')->first();

        if (!$lastTestCase) {
            $nextNumber = 1;
        } else {
            // Extract the last number from ticket_no (assuming it's always like TC001)
            $lastNumber = (int)str_replace('TC', '', $lastTestCase->ticket_no);
            $nextNumber = $lastNumber + 1;
        }

        return 'TC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
