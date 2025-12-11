<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ApplyLoan extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'apply_loans';

    protected $fillable = [
        'full_name',
        'contact',
        'loan_amount',
        'monthly_income',
        'property_city',
        'loan_category_main',
        'loan_category_sub', 
    ];
}
