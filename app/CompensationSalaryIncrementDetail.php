<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompensationSalaryIncrementDetail extends Model
{
    protected $table = 'compensation_salary_increment_details';
    protected $fillable = [
        'compensation_id',
        'increment_category',
        'increment_trigger',
        'last_increment_date',
        'eligibility_remarks',
        'increment_method',
        'increment_percentage',
        'current_basic_salary',
        'current_gross_salary',
        'current_housing_allowance',
        'current_transport_allowance',
        'current_other_allowances',
        'revised_basic_salary',
        'revised_gross_salary',
        'revised_housing_allowance',
        'revised_transport_allowance',
        'revised_other_allowances',
        'monthly_cost_impact',
        'annual_cost_impact',
        'budget_impact_justification',
    ];

    protected $dates = ['last_increment_date', 'created_at', 'updated_at'];
    protected $casts = [
        'increment_percentage' => 'decimal:2',
        'current_basic_salary' => 'decimal:2',
        'current_gross_salary' => 'decimal:2',
        'current_housing_allowance' => 'decimal:2',
        'current_transport_allowance' => 'decimal:2',
        'current_other_allowances' => 'decimal:2',
        'revised_basic_salary' => 'decimal:2',
        'revised_gross_salary' => 'decimal:2',
        'revised_housing_allowance' => 'decimal:2',
        'revised_transport_allowance' => 'decimal:2',
        'revised_other_allowances' => 'decimal:2',
        'monthly_cost_impact' => 'decimal:2',
        'annual_cost_impact' => 'decimal:2',
    ];

    // Relationships
    public function compensation()
    {
        return $this->belongsTo('App\CompensationRole', 'compensation_id');
    }
}
