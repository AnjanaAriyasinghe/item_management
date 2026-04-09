<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\ChequeBookDetails;
use App\Models\Company;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Vendor;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use NumberToWords\NumberToWords;
use Yajra\DataTables\DataTables;
use setasign\Fpdi\Fpdi;


class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:finance-expenses-payment-module|finance-expenses-payment-create')->only('index');
        // $this->middleware('permission:admin-common-vendor-edit')->only(['edit', 'update']);
        // $this->middleware('permission:admin-common-vendor-delete')->only('destroy');
    }
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {

                if ($request->company_id) {
                    $data = Expense::with('category', 'sub_category', 'approved_by', 'vendor', 'company')
                        ->where('status', ['approved'])
                        ->whereIn('paymnet_status', ['pending', 'partially'])
                        ->latest('id')

                        ->when($request->company_id != 'all', function ($query) use ($request) {
                            $query->whereHas('company', function ($query) use ($request) {
                                $query->where('id', $request->company_id);
                            });
                        });
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('company', function ($data) {
                        return $data->company?->name

                            ? '<span class="badge bg-success text-center">' . $data->company->name . '</span>'
                            : '';
                    })
                    ->addColumn('status', function ($data) {
                        // Define status and corresponding badge classes
                        $status = $data->status;
                        switch ($status) {
                            case 'pending':
                                $badge = '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 'approved':
                                $badge = '<span class="badge bg-success">Approved</span>';
                                break;
                            case 'rejected':
                                $badge = '<span class="badge bg-danger">Rejected</span>';
                                break;
                            case 'paid':
                                $badge = '<span class="badge bg-success-1">Paid</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }

                        return $badge;
                    })
                    ->addColumn('paymnet_status', function ($data) {
                        // Define status and corresponding badge classes
                        $status = $data->paymnet_status;
                        switch ($status) {
                            case 'pending':
                                $badge = '<span class="badge bg-warning">Pending</span>';
                                break;
                            case 'partially':
                                $badge = '<span class="badge bg-primary">Partially</span>';
                                break;
                            case 'complete':
                                $badge = '<span class="badge bg-success-1">Complete</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }

                        return $badge;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('finance-expenses-approval-module')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        if (auth()->user()->can('finance-expenses-payment-create')) {
                            $buttons .= ' <button class="btn btn-secondary btn-sm btnPayment" data-bs-toggle="modal" data-bs-target="#createModel" title="Payment" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-file-symlink"></i></button>';
                        }
                        return $buttons;
                    })
                    ->rawColumns(['role', 'action', 'paymnet_status', 'status', 'company'])
                    ->make(true);
            }
            if (\auth()->user()->hasRole('Super Admin')) {
                $companies = Company::all();
            } else {
                $companies = auth()->user()->companies;
            }
            $defaultCompany = auth()->user()->defaultCompany;
            return view('pages.finance.payment.index', ['companies' => $companies, 'defaultCompany' => $defaultCompany->id]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // dd($request->all());
            DB::beginTransaction();
            $input = $request->all();
            $cheque = ChequeBookDetails::find($request->cheque_book_detail_id);
            $input['created_by'] = auth()->user()->id;
            $input['cheque_book_detail_id'] = $cheque->id;
            $input['cheque_number'] = $cheque->cheque_number;
            $input['payment_date'] = now();
            $input['status'] = 'issued';
            $expense = Expense::find($request->expense_id);
            $input['vendor_id'] = $expense->vendor_id;
            $payment = Payment::create($input);
            if ($payment) {
                $paymnet_status = 'partially';
                $expense->balance = $expense->balance - $request->amount;
                $expense->save();
                if ($expense->balance > 0) {
                    $paymnet_status = 'partially';
                } else if ($expense->balance == 0 | $expense->balance < 0) {
                    $paymnet_status = 'complete';
                }
                $expense->paymnet_status = $paymnet_status;
                $expense->save();
                $cheque_book = ChequeBookDetails::find($cheque->id);
                $cheque_book->amount = $payment->amount;
                $cheque_book->payment_id = $payment->id;
                $cheque_book->cheque_date = $payment->cheque_date;
                $cheque_book->issue_date = $payment->payment_date;
                $cheque_book->payee_name = $request->payee_name;
                $cheque_book->payment_condition = $request->payment_condition;
                $cheque_book->validity_period = $request->validity_period;
                $cheque_book->signatory_id = $request->signatory_id;
                $cheque_book->referance_no = $request->referance_no;
                $cheque_book->status = "issued";
                $cheque_book->issued_by = auth()->user()->id;
                $cheque_book->update();
            }
            DB::commit();
            if ($payment) {
                $vendor = Vendor::find($payment->vendor_id);
                $vendor_name = $vendor->name;
                if ($cheque_book->payee_name == 1) {
                    if ($vendor->nic) {
                        $vendor_name = $vendor->name . ' / ' . $vendor->nic;
                    } else {
                        $vendor_name = $vendor->name;
                    }
                }
                if ($cheque_book->payment_condition == 1) {
                    $payment_condition = "A/C PAYEE ONLY";
                } else if ($cheque_book->payment_condition == 2) {
                    $payment_condition = "A/C PAYEE ONLY";
                    $payment_condition2 = "NOT NEGOTIABLE";
                } else {
                    $payment_condition = null;
                }
                if ($cheque_book->validity_period == 1) {
                    $validity_period = "VALID ONLY FOR 30 DAYS";
                } elseif ($cheque_book->validity_period == 2) {
                    $validity_period = "VALID ONLY FOR 60 DAYS";
                } else {
                    $validity_period = null;
                }
                $company = Company::first();
                $logo = $company->logo;
                $data = [
                    'date' => Carbon::parse($payment->cheque_date)->format('dmY'),
                    'expense_id' => $request->expense_id,
                    'payee_name' => $vendor_name,
                    'amount' => $payment->amount,
                    'payment_condition' => $payment_condition,
                    'payment_condition2' => $payment_condition2,
                    'validity_period' => $validity_period,
                    'signature' => $cheque_book->signatory_id,
                    'logo' => $logo
                ];

                DB::commit();
                // $this->printCheque($data);
                // $data = [
                //     "date" => "21052025",
                //     "payee_name" => "Sri Lanka Telecom PLC",
                //     "amount" => "150570",
                //     "payment_condition" => 'A/C PAYEE ONLY',
                //     "validity_period" => 'VALID ONLY FOR 30 DAYS',
                //     "signature" => "5",
                //     "logo" => "company_logo/67e38e23472d6.jpg",
                // ];
                return $this->generatePdf($data);
                // return response()->json(['pdf_url' => $chequeUrl, 'status' => false], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }
    public function amountOnWords(Request $request)
    {
        try {
            $amount = $request->amount;
            $integerPart = floor($amount);
            $decimalPart = round(($amount - $integerPart) * 100);

            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('en');
            $integerPartInWords = ucwords($numberTransformer->toWords($integerPart));
            $decimalPartInWords = $decimalPart > 0 ? ucwords($numberTransformer->toWords($decimalPart)) : '';

            $amountInWords = $decimalPartInWords
                ? $integerPartInWords . ' and ' . $decimalPartInWords . ' Cents Only'
                : $integerPartInWords . ' Only';

            return response()->json(['amount_on_words' => $amountInWords, 'status' => true], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
    public function printCheque($data)
    {
        try {
            $amountWords = $this->amountOnWords2($data['amount']);
            // $dateParts = explode('/', $data['date']);
            // $formattedDateString = $dateParts[2] . '-' . $dateParts[1] . '-' . $dateParts[0];
            // $data['date'] = new DateTime($formattedDateString);
            // $dateFormatted = $data['date']->format('dmY');
            // $data['logo']="storage/$data[logo]";
            $pdf = Pdf::loadView('pages.print.print', [
                'date' => '10022025',
                'payee_name' => $data['payee_name'],
                'amount' => number_format($data['amount'], 2),
                'amountWords' => $amountWords,
                'payment_condition' => $data['payment_condition'],
                'validity_period' => $data['validity_period'],
                'signature' => $data['signature'],
                'logo' => $data['logo']
            ]);
            // $pdf->setPaper([0, 0, 504, 252]);
            $fileName = 'cheque_' . time() . '.pdf';

            $filePath = storage_path('app/public/temp/' . $fileName);
            $pdf->save($filePath);

            return asset('storage/temp/' . $fileName);
        } catch (\Throwable $th) {
            Log::error('PDF generation failed: ' . $th->getMessage());
            return null;
        }
    }

    public function amountOnWords2($amount)
    {
        try {
            $amount = $amount;
            $integerPart = floor($amount);
            $decimalPart = round(($amount - $integerPart) * 100);

            $numberToWords = new NumberToWords();
            $numberTransformer = $numberToWords->getNumberTransformer('en');
            $integerPartInWords = ucwords($numberTransformer->toWords($integerPart));
            $decimalPartInWords = $decimalPart > 0 ? ucwords($numberTransformer->toWords($decimalPart)) : '';

            $amountInWords = $decimalPartInWords
                ? $integerPartInWords . ' and ' . $decimalPartInWords . ' Cents Only'
                : $integerPartInWords . ' Only';
            return $amountInWords;
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }


    public function generatePdf($data)
    {
        try {

            // dd($data);
            $expense = Expense::find($data['expense_id']);
            $company = $expense->company;
            $logo = $company->logo;
            $logoPath = public_path("storage/$logo");
            $company_name = $company->name;
            // $company_name = "Vital One (Pvt) Ltd";
            $pv_no = $company->pv_no ?? '';
            $amountWords = $this->amountOnWords2($data['amount']);
            $amountInWords = '**' . $amountWords . '**';
            $payeeName = $data['payee_name'];
            $amount = number_format($data['amount'], 2);
            $payment_condition = $data['payment_condition'];
            $payment_condition2 = $data['payment_condition2'];

            $validity_period = $data['validity_period'];
            $date = $data['date'];
            // $xx = 205;
            // $yy = 12;
            $xx = 215;
            $yy = 12;
            $spacing = 3.5;

            $pdf = new Fpdi();

            // Convert inches to millimeters (1 inch = 25.4 mm)
            // $customWidth = 7 * 25.4;  // 7 inches
            // $customHeight = 3.5 * 25.4; // 3.5 inches
            $customWidth = 211;  // A4 width in mm
            $customHeight = 297; // A4 height in mm
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->AddPage('L', [$customWidth, $customHeight]); // 'L' for Landscape mode


            // Get updated page width
            $pageWidth = $pdf->GetPageWidth();
            $logoWidth = $pageWidth * 0.08;

            $filePath = resource_path('views/print/empty_1.pdf');
            $pdf->setSourceFile($filePath);
            $template = $pdf->importPage(1);
            $pdf->useTemplate($template, 0, 0, $customWidth, $customHeight);


            $pdf->SetFont('Helvetica', '', 14);
            // Print Date
            for ($i = 0; $i < strlen($date); $i++) {
                $pdf->SetXY($xx, $yy);
                $pdf->Write(0, $date[$i]);
                $xx += $pdf->GetStringWidth($date[$i]) + $spacing;
            }
            $pdf->SetFont('Helvetica', '', 8); // Reduce font size for smaller page
            // Payee Name
            // $pdf->SetXY(95, 25);
            $pdf->SetXY(105, 25);

            $pdf->Write(0, "**$payeeName**");

            // Amount in Words
            // $x = 101;
            $x = 111;
            $y = 32;
            $lineHeight = 6;
            $maxLineLength = 60; // Adjusted for landscape
            $words = explode(' ', $amountInWords);
            $currentLine = '';

            foreach ($words as $word) {
                $testLine = $currentLine . ($currentLine ? ' ' : '') . $word;
                if (strlen($testLine) > $maxLineLength) {
                    $pdf->SetXY($x, $y);
                    $pdf->Cell(0, $lineHeight, trim($currentLine), 0, 1);
                    $y += $lineHeight;
                    $currentLine = $word;
                } else {
                    $currentLine = $testLine;
                }
            }
            if (!empty($currentLine)) {
                $pdf->SetXY($x, $y);
                $pdf->Cell(0, $lineHeight, trim($currentLine), 0, 1);
            }

            $pdf->SetFont('Helvetica', '', 12);
            // Print Amount
            $pdf->SetXY(212, 41);
            $pdf->Write(0, "**$amount**");
            $pdf->SetFont('Helvetica', '', 8);
            $pdf->SetXY(212, 50);
            $pdf->Write(0, $company_name);
            $pdf->SetXY(215, 55);
            $pdf->Write(0, $pv_no);
            // Logo Position Adjusted
            if ($logoPath && file_exists($logoPath)) {
                $pdf->Image($logoPath, 177, 60, $logoWidth, 0);
            }


            // Payment Condition
            if ($payment_condition) {

                if ($payment_condition2) { //check not negotiable
                    $pdf->SetXY(144, 50);
                    $pdf->Write(0, "_________________");

                    $pdf->SetXY(144, 55);
                    $pdf->Write(0, $payment_condition);

                    $pdf->SetXY(144, 59);
                    $pdf->Write(0, $payment_condition2);

                    $pdf->SetXY(144, 62);
                    $pdf->Write(0, "_________________");
                } else {
                    $pdf->SetXY(144, 50);
                    $pdf->Write(0, "_________________");
                    $pdf->SetXY(144, 53);
                    $pdf->Write(0, $payment_condition);
                    $pdf->SetXY(144, 54);
                    $pdf->Write(0, "_________________");
                }
            }
            // Validity Period
            if ($validity_period) {
                $pdf->SetXY(155, 15);
                $pdf->Write(0, "_______________________");
                $pdf->SetXY(155, 19);
                $pdf->Write(0, $validity_period);
                $pdf->SetXY(155, 20);
                $pdf->Write(0, "_______________________");
            }
            // Signature Fields

            if ($data['signature'] == '1') {

                $pdf->SetXY(215, 68);
                // $pdf->Write(0, "--------------------");
                $pdf->SetXY(219.5, 71);
                $pdf->Write(0, "Director");
            }

            if ($data['signature'] == '2') {

                $pdf->SetXY(213, 68);
                // $pdf->Write(0, "-------------------");
                $pdf->SetXY(208, 71);
                $pdf->Write(0, "Director");

                $pdf->SetXY(234, 68);
                // $pdf->Write(0, "--------------------");
                $pdf->SetXY(229, 71);
                $pdf->Write(0, "Director");
            }

            if ($data['signature'] == '3') {
                $pdf->SetXY(202, 68);
                // $pdf->Write(0, "-------------------");
                $pdf->SetXY(205, 71);
                $pdf->Write(0, "Director");

                $pdf->SetXY(223, 68);
                // $pdf->Write(0, "-------------------");
                $pdf->SetXY(226, 71);
                $pdf->Write(0, "Director");

                $pdf->SetXY(244, 68);
                // $pdf->Write(0, "--------------------");
                $pdf->SetXY(246, 71);
                $pdf->Write(0, "Director");
            }

            if ($data['signature'] == '4') {
                $pdf->SetXY(223, 68);
                // $pdf->Write(0, "-------------------");
                $pdf->SetXY(226, 71);
                $pdf->Write(0, "Director");

                $pdf->SetXY(244, 68);
                // $pdf->Write(0, "--------------------");
                $pdf->SetXY(246, 71);
                $pdf->Write(0, "Authorized");
            }
            if ($data['signature'] == '5') {

                $pdf->SetXY(202, 68);
                // $pdf->Write(0, "-------------------");
                $pdf->SetXY(205, 71);
                $pdf->Write(0, "Director");

                $pdf->SetXY(223, 68);
                // $pdf->Write(0, "-------------------");
                $pdf->SetXY(226, 71);
                $pdf->Write(0, "Director");

                $pdf->SetXY(244, 68);
                // $pdf->Write(0, "--------------------");
                $pdf->SetXY(246, 71);
                $pdf->Write(0, "Authorized");
            }
            // $filePath = storage_path('app/public/temp/GeneratedPDF.pdf');
            // if (file_exists($filePath)) {
            //     unlink($filePath);
            // }
            // $pdf->Output('F', $filePath);
            // // dd($filePath);
            // // Return the URL to the saved PDF
            // return response()->json(['pdf_url' => asset('storage/temp/GeneratedPDF.pdf')], 200);


            $directory = storage_path('app/public/temp');

            if (!file_exists($directory)) {
                mkdir($directory, 0777, true);
            }

            $files = glob($directory . '/*.pdf');
            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            $fileName = 'GeneratedPDF_' . time() . '.pdf';
            $filePath = $directory . '/' . $fileName;

            $pdf->Output('F', $filePath);

            return response()->json([
                'pdf_url' => asset('storage/temp/' . $fileName)
            ], 200);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
