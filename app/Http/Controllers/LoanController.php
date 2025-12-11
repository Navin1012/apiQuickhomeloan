<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Loan;

class LoanController extends Controller
{
   
public function store(Request $request)
{
    try {
        $userId = auth()->id();

        $application = Loan::where('user_id', $userId)->first();

        if (!$application) {
            $application = Loan::create([
                'user_id' => $userId,
                'step_completed' => 0,
                'data' => []
            ]);
        }

        $step = (int)$request->step;

        if ($step == 1) {

            if ($application->step_completed >= 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Step 1 already completed. Go to Step 2'
                ]);
            }

            $request->validate([
                'pan' => 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                'full_name' => 'required|string|max:255',
                'dob' => 'required|date',
                'city' => 'required',
                'employment_type' => 'required',
            ]);

            $application->update([
                'step_completed' => 1,
                'data' => array_merge($application->data, $request->all())
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Step 1 done. Move to Step 2.'
            ]);
        }


        if ($step == 2) {

            if ($application->step_completed < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Step 1 incomplete'
                ], 400);
            }

            if ($application->step_completed >= 2) {
                return response()->json([
                    'success' => true,
                    'message' => 'Step 2 already completed. Go to Step 3'
                ]);
            }

            $request->validate([
                'income' => 'required|numeric|min:10000',
                'existing_emi' => 'required|numeric|min:0',
                'loan_amount' => 'required|numeric|min:100000',
            ]);

            $application->update([
                'step_completed' => 2,
                'data' => array_merge($application->data, $request->all())
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Step 2 completed. Move to Step 3.'
            ]);
        }


        if ($step == 3) {

            if ($application->step_completed < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Step 2 incomplete'
                ], 400);
            }

            $request->validate([
                'property_stage' => 'required'
            ]);

            $finalData = array_merge($application->data, $request->all());

            $application->update([
                'step_completed' => 3,
                'data' => $finalData
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application completed',
                'data' => $application
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid step number'
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Server error',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
