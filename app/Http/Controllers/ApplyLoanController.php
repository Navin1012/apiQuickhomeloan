<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApplyLoan;

class ApplyLoanController extends Controller
{
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'contact' => 'required|string|max:255',
                'loan_amount' => 'required|numeric',
                'monthly_income' => 'required|numeric',
                'property_city' => 'required|string|max:255',
                'loan_category_main' => 'required|string',
                'loan_category_sub' => 'required|string',
            ]);


            ApplyLoan::create($validated);

            return response()->json([
                "success" => true,
                "message" => "Loan application stored successfully!"
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                "success" => false,
                "message" => "Validation failed",
                "errors" => $e->errors()
            ], 422);

        } catch (\Exception $e) {

            return response()->json([
                "success" => false,
                "message" => "Something went wrong on the server",
                "error" => $e->getMessage()
            ], 500);
        }
    }

}
