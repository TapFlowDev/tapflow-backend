<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Final_proposal;
use App\Models\Milestone;
use App\Models\payments;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentsController extends Controller
{
    function makePayment(Request $request)
    {
        // return $request->user();
        try {

            $walletTransactionsObj = new WalletsTransactionsController;
            $userData = $this->checkUser($request);
            $condtion = $userData['exist'] == 1 && $userData['privileges'] == 1 && $userData['type'] == 2;
            if (!$condtion) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $rules = array(
                "milestone_id" => "required|exists:milestones,id",
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $responseData = $validator->errors();
                $response = Controller::returnResponse(101, "Validation Error", $responseData);
                return (json_encode($response));
            }
            /*
            validate milestone
            */
            $milstoneInfo = Milestone::where('id', '=', $request->milestone_id)->get()->first();
            //check if milestone exists
            if (!$milstoneInfo) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            $projectId = $milstoneInfo->project_id;
            $contractId = $milstoneInfo->final_proposal_id;
            /**
             * validate final proposal
             */
            $contract = Final_proposal::where('id', '=', $contractId)->where('project_id', '=', $projectId)->get()->first();
            //check if milestone exists
            if (!$contract) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            if ($contract->status != 1) {
                $response = Controller::returnResponse(422, "final proposal is not accepted yet", []);
                return (json_encode($response));
            }

            /*
            validate project
            */
            //check if project is valid
            $projectInfo = Project::where('id', '=', $projectId)->where('company_id', '=', $userData['group_id'])->get()->first();
            if (!$projectInfo) {
                $response = Controller::returnResponse(401, "unauthorized user", []);
                return (json_encode($response));
            }
            // check if team id exists
            if (!$projectInfo->team_id) {
                $response = Controller::returnResponse(422, "project is not active", []);
                return (json_encode($response));
            }
            // check if project active
            if ($projectInfo->status != 4 && $projectInfo->status != 1) {
                $response = Controller::returnResponse(422, "project is not active", []);
                return (json_encode($response));
            }

            /*
            first check if milestone is a downpayment
            if not check if milestone is accepted
            =>
            if milestone is not downpayment and not active error will be returned 
            */
            if ($milstoneInfo->down_payment != 1 && $milstoneInfo->status != 3) {
                $response = Controller::returnResponse(422, "milestone is not accepted yet please review and accept milestone before payment", []);
                return (json_encode($response));
            }

            //check if milestone is paid
            if ($milstoneInfo->is_paid == 1) {
                $response = Controller::returnResponse(422, "You already paid for this milestone", []);
                return (json_encode($response));
            }
            /*
            create payment and make transaction from company wallet to agency wallet
            */
            // calculate prices
            $milestonePrice = $milstoneInfo->price;
            $tapflowFee = number_format($milestonePrice * 0.05, 2, '.', '');
            $total = number_format($milestonePrice + $tapflowFee, 2, '.', '');
            $agencyTotal = number_format($milestonePrice - $tapflowFee, 2, '.', '');
            // create payment
            $paymentData = array(
                'user_id' => $userData['user_id'],
                'company_id' => $userData['group_id'],
                'agency_id' => $projectInfo->team_id,
                'project_id' => $projectInfo->id,
                'milestone_id' => $milstoneInfo->id,
                'milestone_price' => $milstoneInfo->price,
                'tapflow_fee' => $tapflowFee,
                'total_price' => $total,
                'agency_total_price' => $agencyTotal,
            );
            $payment = payments::create($paymentData);
            // make wallet transaction
            $paymentStatus = $walletTransactionsObj->makePaymentTransactionWithdraw($payment);
            if ($paymentStatus['paymentStatus'] == -1) {
                $response = Controller::returnResponse(500, 'Something Wrong ', $paymentStatus['paymentMsg']);
                return (json_encode($response));
            }
            //$agencyTransaction = $this->makeWalletDeposit($payment);
            /**
             * check if milestone downpayment 
             * if milestone downpayment agency will not get the deposit untill they submit milestone
             * else if milestone is not downpayment and submitted we make sure $paymenStatus = 1 so we make sure 
             *  the transaction was made successfully 
             */
            $isProjectActive = $projectInfo->status;
            if ($paymentStatus['paymentStatus'] == 1) {
                $milstoneInfo->is_paid = 1;
                $milstoneInfo->save();
                if ($milstoneInfo->down_payment == 1) {
                    $payment->status = $paymentStatus['paymentStatus'];
                    $payment->save();
                } else {
                    $agencyPaymentStatus = $walletTransactionsObj->makePaymentTransactionDeposit($payment);
                    $payment->status = 2;
                    $payment->save();
                    /**
                     * MUST MAKE LOG FILE FOR TRANSACTIONS IF ERROR ACURED
                     */
                }
                /**
                 * check other milestones to make project active
                 */
                $isProjectActive = $this->makeProjectActive($contract);
            }

            $returnData = array(
                'payment' => $payment,
                'msg' => $paymentStatus['paymentMsg'],
                'projectActive' => $isProjectActive
            );
            $response = Controller::returnResponse($paymentStatus['responseCode'], $paymentStatus['paymentMsg'], $returnData);
            return (json_encode($response));
        } catch (Exception $error) {
            $response = Controller::returnResponse(500, 'Something Wrong ', $error->getMessage());
            return (json_encode($response));
        }
    }
    function makeProjectActive($final)
    {
        $finalId = $final->id;
        // $final = Final_proposal::where('id', '=', $finalId)->get()->first();
        $project = Project::where('id', '=', $final->project_id)->get()->first();
        // $isPaidArray = array_column($milestones, 'is_paid');
        // $statusArray = array_column($milestones, 'status');
        //return $milestones;
        /**
         * check if project is alreeady active
         * if project is active then check all milestone if submitted and paid to make project completed
         * else if project not active check milestones if all paid to make project active
         */
        $projectStatus = $project->status;
        if ($project->status == 1) {
            $milestonesCount = Milestone::where('final_proposal_id', '=', $finalId)->count();
            $milestones = Milestone::where('final_proposal_id', '=', $finalId)->where('is_paid', '=', 1)->where('status', '=', 3)->count();
            if ($milestones==$milestonesCount) {
                $projectStatus = 3;   
                $project->status = $projectStatus;
                $project->save();
            }
        } elseif ($project->status == 4) {
            $milestones = Milestone::where('final_proposal_id', '=', $finalId)->where('down_payment', '=', 1)->count();
            $milestonesPaid = Milestone::where('final_proposal_id', '=', $finalId)->where('down_payment', '=', 1)->where('is_paid', '=', 1)->count();
            if ($milestones==$milestonesPaid) {
                $projectStatus = 1;   
                $project->status = $projectStatus;
                $project->save();
            }
        }
        return $project->status;
    }
}
