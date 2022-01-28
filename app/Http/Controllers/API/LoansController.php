<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Loan as LoanResource;
use App\Models\Loan;
use Validator;

class LoansController extends BaseController
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
          'approved_amount' => 'required|integer',
          'loan_tenor' => 'required|integer',
          'currency' => 'required|string',
          'origination_fee_percentage' => 'required|between:1,6',
          'interest_rate' => 'required|between:1,4',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        try {
            // check if user already has a pending or approved (but not fully repaid) loan
            $loan_exists = Loan::where('user_id', auth()->user()->id)
                ->whereIn('status', [Loan::LOAN_STATUS_PENDING, Loan::LOAN_STATUS_APPROVED])
                ->exists();

            // if no pending or approved (but not fully repaid) loan, create the loan
            if ($loan_exists === false) {
                $loan = Loan::create(request()->all());
                return $this->sendResponse('Loan created.', ['loan' => LoanResource::make($loan)]);
            }

            return $this->sendError('You already have a pending or unpaid loan.', [], 401);
        } catch (\Exception $e) {
            return $this->sendError('Something went wrong! Please try again.');
        }
    }

    public function index()
    {
        $loans = Loan::with('user')->where('user_id', auth()->user()->id)->paginate(10);

        return $this->sendResponse('Loan history loaded.', ['loans' => LoanResource::collection($loans)]);
    }

    public function show($id)
    {
        $loan = Loan::with('user', 'repayments')->findOrFail($id);

        return $this->sendResponse('Loan loaded.', ['loan' => LoanResource::make($loan)]);
    }
}
