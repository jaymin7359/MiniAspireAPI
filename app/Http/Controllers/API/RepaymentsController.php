<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Repayment as RepaymentResource;
use App\Models\Loan;
use Validator;

class RepaymentsController extends BaseController
{
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
          'repayment_amount' => ['required', 'regex:/^\d*(\.\d{2})?$/'],
          'repayment_method' => 'required|string',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = request()->only([
            'repayment_amount', 'repayment_method'
        ]);
        $data['paid_at'] = date('Y-m-d H:i:s');

            // check if user has any approved (but not fully repaid) loan
            $loan = Loan::select(['id', 'approved_amount', 'interest_rate', 'loan_tenor'])
                ->where('user_id', auth()->user()->id)
                ->where('status', Loan::LOAN_STATUS_APPROVED)
                ->first();

            // if approved (but not fully repaid) loan found, proceed to create a repayment for that loan
            if ($loan) {
                try {
                    $total_interest = $loan->approved_amount * ($loan->interest_rate * $loan->loan_tenor / 100);
                    $total_amount_repayable = $loan->approved_amount + $total_interest;
                    $weekly_total_repayment = number_format($total_amount_repayable / $loan->loan_tenor, 2, '.', '');
                    $repayment_amount = number_format($data['repayment_amount'], 2, '.', '');
                    if ($weekly_total_repayment === $repayment_amount) {
                        $repayment = $loan->repayments()->create($data);
                        return $this->sendResponse('Repayment created.', ['repayment' => RepaymentResource::make($repayment)]);
                    }

                    return $this->sendError('You must pay a repayment amount of ' . number_format($weekly_total_repayment, 2));
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }

            return $this->sendError('No unpaid loan found to make a repayment.', [], 401);
    }
}
