<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovedRequest;
use App\Http\Requests\BankAccountRequest;
use App\Http\Requests\ChequeBookRequest;
use App\Http\Requests\ChequeCancelledRequest;
use App\Http\Requests\ChequeClearedRequest;
use App\Http\Requests\RejectRequest;
use App\Models\AuthorizedSignatory;
use App\Models\BankAccount;
use App\Models\ChequeBook;
use App\Models\ChequeBookDetails;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;
use Yajra\DataTables\DataTables;

class ChequeBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:admin-common-cheque_book-module|admin-common-cheque_book-create')->only('index');
        $this->middleware('permission:admin-common-cheque_book-edit')->only(['edit', 'update']);
        $this->middleware('permission:admin-common-cheque_book-delete')->only('destroy');
        $this->middleware('permission:admin-common-cheque_book-view')->only(['show', 'details']);
        $this->middleware('permission:admin-common-cheque_book-approve')->only('approval');
        $this->middleware('permission:admin-common-cheque_book-reject')->only('reject');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = ChequeBook::with('bank_account.bank', 'bank_account.branch', 'created_user', 'approved_user_name', 'reject_user_name')->latest('id');
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('created_at', function ($data) {
                        return $data->updated_at ?? $data->created_at;
                    })
                    ->addColumn('approved_user_name', function ($data) {
                        return $data->approved_user_name->name ?? '-';
                    })
                    ->addColumn('reject_user_name', function ($data) {
                        return $data->reject_user_name->name ?? '-';
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
                            case 'reject':
                                $badge = '<span class="badge bg-danger">Rejected</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }

                        return $badge;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-cheque_book-view')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        if ($data->status != 'approved' && $data->status != 'reject') {
                            if (auth()->user()->can('admin-common-cheque_book-edit')) {
                                $buttons .= ' <button class="btn btn-warning btn-sm btnEdit" data-bs-toggle="modal" data-bs-target="#createModel" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-edit f-20"></i></button>';
                            }
                        }
                        if ($data->status == 'pending') {
                            if (auth()->user()->can('admin-common-cheque_book-approve')) {
                                $buttons .= ' <button class="btn btn-success btn-sm btnApproval"  data-bs-toggle="modal" data-bs-target="#approvalModel" title="Approval" data-id=\'' . json_encode($data->id) . '\'><i class="ti ti-check"></i></button>';
                            }
                        }
                        if ($data->status != 'approved' && $data->status != 'reject') {
                            if (auth()->user()->can('admin-common-cheque_book-reject')) {
                                $buttons .= ' <button class="btn btn-danger btn-sm btnReject"  data-bs-toggle="modal" data-bs-target="#rejectModel" title="Reject" data-id=\'' . json_encode($data->id) . '\'><i class="ti ti-x"></i></button>';
                            }
                        }
                        if ($data->status == 'pending') {
                            if (auth()->user()->can('admin-common-cheque_book-delete')) {
                                $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.cheque_books.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                            }
                        }
                        return $buttons;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('pages.admin.chequeBook.index');
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
    public function store(ChequeBookRequest $request)
    {
        try {
            DB::beginTransaction();
            $exists=ChequeBook::where('bank_account_id',$request->bank_account_id)->where('status','pending')->exists();
            if($exists){
                return response()->json(['message' => 'Please approve or reject the already-added cheque book request.', 'status' => false], 500);
            }
            $bankAccount = BankAccount::find($request->bank_account_id);
            $input = $request->all();
            $input['created_by'] = auth()->user()->id;
            $input['account_number'] = $bankAccount->account_no;
            $cheque_book = ChequeBook::create($input);
            DB::commit();
            if ($cheque_book) {
                return response()->json(['message' => "Cheque Book created successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ChequeBook $chequeBook)
    {
        try {
            $chequeBook = ChequeBook::with(
                'bank_account.bank',
                'bank_account.branch',
                'created_user',
                'updated_by',
                'deleted_by',
                'approved_user_name',
                'reject_user_name',
            )->find($chequeBook->id);
            return response()->json([
                'chequeBook' => $chequeBook,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChequeBook $chequeBook)
    {
        try {
            $chequeBook = ChequeBook::find($chequeBook->id);
            return response()->json([
                'chequeBook' => $chequeBook,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(ChequeBookRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $cheque_book = ChequeBook::findOrFail($id);
            $input = $request->all();
            $input['updated_by'] = auth()->user()->id;
            $cheque_book->update($input);
            DB::commit();
            return response()->json(['message' => "Cheque Book updated successfully...", 'status' => true], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChequeBook $chequeBook)
    {
        try {
            $chequeBook->update(['deleted_by' => auth()->user()->id]);
            $chequeBook->delete();
            if ($chequeBook) {
                return response()->json(['message' => "Cheque deleted successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function getAccounts()
    {
        try {
            $bankAccount = BankAccount::all();
            return response()->json([
                'bankAccount' => $bankAccount,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
    public function getAccountNo($id)
    {
        try {
            $bankAccount = BankAccount::find($id);
            $exists=ChequeBook::where('bank_account_id',$bankAccount->id)->where('status','pending')->exists();
            if($exists){
                return response()->json(['message' => 'Please approve or reject the already-added cheque book request.', 'status' => false], 500);
            }
            return  $bankAccount->account_no;
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function approval(ApprovedRequest $request)
    {
        try {
            $cheque_book = ChequeBook::find($request->id);
            $cheque_book->approval_comment = $request->approval_comment;
            $cheque_book->approved_user = auth()->user()->id;
            $cheque_book->approved_date = now();
            $cheque_book->status = 'approved';
            $cheque_book->update();

            $chequeCount = $cheque_book->number_of_cheque;
            $cheque_nu = $cheque_book->start_number;
            $paddingLength = strlen($cheque_nu);
            if ($chequeCount > 0) {
                for ($i = 1; $i <= $chequeCount; $i++) {
                    // Ensure unique cheque_number
                    while (ChequeBookDetails::where('cheque_number', str_pad($cheque_nu, $paddingLength, '0', STR_PAD_LEFT))->exists()) {
                        $cheque_nu += 1; // Increment the number if it already exists
                    }

                    // Create the new cheque detail
                    ChequeBookDetails::create([
                        'cheque_book_id' => $cheque_book->id,
                        'cheque_number' => str_pad($cheque_nu, $paddingLength, '0', STR_PAD_LEFT), // Ensure proper padding
                    ]);

                    $cheque_nu += 1; // Increment the number for the next iteration
                }
                return response()->json(['message' => "Cheque Book approved successfully...", 'status' => true], 200);
            }
             else {
                return response()->json(['message' => 'Please contact system admin', 'status' => false], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function reject(RejectRequest $request)
    {
        try {
            $cheque_book = ChequeBook::find($request->id);
            $cheque_book->status = 'reject';
            $cheque_book->reject_comment = $request->reject_comment;
            $cheque_book->rejected_date = now();
            $cheque_book->reject_user = auth()->user()->id;
            $cheque_book->update();
            if ($cheque_book) {
                return response()->json(['message' => "Cheque Book rejected successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }


    public function details(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = ChequeBookDetails::with('cheque_book.bank_account.bank', 'cheque_book.bank_account.branch')
                    ->when($request->cheque_book_id && $request->cheque_book_id !== 'all', function ($query) use ($request) {
                        $query->where('cheque_book_id', $request->cheque_book_id);
                    })
                    ->when($request->status && $request->status !== 'all', function ($query) use ($request) {
                        $query->where('status', $request->status);
                    });
                if (!$request->cheque_book_id && !$request->status) {
                    $data = [];
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('status', function ($data) {
                        // Define status and corresponding badge classes
                        $status = $data->status;
                        switch ($status) {
                            case 'pending':
                                $badge = '<span class="badge bg-primary">Available</span>';
                                break;
                            case 'cleared':
                                $badge = '<span class="badge bg-success-1">Cleared</span>';
                                break;
                            case 'cancelled':
                                $badge = '<span class="badge bg-danger">Cancelled</span>';
                                break;
                            case 'issued':
                                $badge = '<span class="badge bg-secondary">Issued</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }
                        return $badge;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-cheque_book-view')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        if ($data->status == 'pending') {
                            if (auth()->user()->can('admin-common-cheque-cancel')) {
                                $buttons .= ' <button class="btn btn-danger btn-sm btnReject" data-bs-toggle="modal" data-bs-target="#rejectModel" title="Cancelled" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-x"></i></i></button>';
                            }
                        }
                        // if ($data->status == 'pending') {
                        //     if (auth()->user()->can('admin-common-cheque_book-delete')) {
                        //         $buttons .= ' <button class="btn btn-danger btn-sm" onclick="handleDelete(\'' . route('admin.cheque_books.destroy', $data['id']) . '\', { _token: \'' . csrf_token() . '\' })"> <i class="ti ti-trash f-20"></i></button>';
                        //     }
                        // }
                        return $buttons;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('pages.admin.chequeBookDetail.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }

    public function getDetailsById($id)
    {
        try {
            $data = ChequeBookDetails::with('cheque_book.bank_account.bank', 'cheque_book.bank_account.branch', 'issued_by', 'cleared_by', 'cancelled_by', 'payment.expense.category', 'payment.expense.sub_category', 'payment.vendor','payment.expense.vendor_account')->find($id);
            return response()->json([
                'book_details' => $data,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function cleared(ChequeClearedRequest $request)
    {
        try {
            $cheque_details = ChequeBookDetails::find($request->id);
            $cheque_details->clear_date = now();
            $cheque_details->cleared_by = auth()->user()->id;
            $cheque_details->cleared_comment = $request->cleared_comment;
            $cheque_details->status = "cleared";
            $cheque_details->update();
            $payment = Payment::where('cheque_book_id', $cheque_details->cheque_book_id)
                ->where('cheque_book_detail_id', $cheque_details->id)
                ->first();
            $payment->status = 'passed';
            $payment->save();
            if ($cheque_details) {
                return response()->json(['message' => "Cheque cleared successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function cancelled(ChequeCancelledRequest $request)
    {
        try {
            $cheque_details = ChequeBookDetails::find($request->id);
            $cheque_details->cancel_date = now();
            $cheque_details->cancelled_by = auth()->user()->id;
            $cheque_details->cancelled_comment = $request->cancelled_comment;
            $cheque_details->status = "cancelled";
            $cheque_details->update();
            if ($cheque_details) {
                return response()->json(['message' => "Cheque Cancelled successfully...", 'status' => true], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function getChequeBooks($id)
    {
        try {
            $book_ids = ChequeBookDetails::where('status', ['pending'])->groupBy('cheque_book_id')->pluck('cheque_book_id');
            return ChequeBook::with('bank_account')->where('bank_account_id', $id)
                ->where('status', 'approved')
                ->whereIn('id', $book_ids)
                ->get();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function getCheque($id){
        try {
         return  ChequeBookDetails::where('status', ['pending'])->where('cheque_book_id',$id)->get();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
    public function getChequeBooksAll()
    {
        try {
            return ChequeBook::with('bank_account')->where('status', ['approved'])->get();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }

    public function cheques(Request $request)
    {
        try {
            if ($request->ajax()) {
                $data = ChequeBookDetails::with('cheque_book.bank_account.bank', 'cheque_book.bank_account.branch')
                    ->when($request->cheque_book_id && $request->cheque_book_id !== 'all', function ($query) use ($request) {
                        $query->where('cheque_book_id', $request->cheque_book_id);
                    })
                    ->when($request->status && $request->status !== 'all', function ($query) use ($request) {
                        $query->where('status', $request->status);
                    });
                if (!$request->cheque_book_id && !$request->status) {
                    $data = [];
                }
                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('id', function () {
                        static $id = 1;
                        return $id++;
                    })
                    ->addColumn('status', function ($data) {
                        // Define status and corresponding badge classes
                        $status = $data->status;
                        switch ($status) {
                            case 'pending':
                                $badge = '<span class="badge bg-primary">Available</span>';
                                break;
                            case 'cleared':
                                $badge = '<span class="badge bg-success-1">Cleared</span>';
                                break;
                            case 'cancelled':
                                $badge = '<span class="badge bg-danger">Cancelled</span>';
                                break;
                            case 'issued':
                                $badge = '<span class="badge bg-secondary">Issued</span>';
                                break;
                            default:
                                $badge = '<span class="badge bg-secondary">Unknown</span>';
                                break;
                        }
                        return $badge;
                    })
                    ->addColumn('action', function ($data) {
                        $buttons = '';
                        if (auth()->user()->can('admin-common-cheque_book-view')) {
                            $buttons .= ' <button class="btn btn-primary btn-sm btnView" data-bs-toggle="modal" data-bs-target="#viewModel" title="Details" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-eye f-20"></i></button>';
                        }
                        if ($data->status == 'issued') {
                            if (auth()->user()->can('admin-common-cheque-clear')) {
                                $buttons .= ' <button class="btn btn-success btn-sm btnApproval" data-bs-toggle="modal" data-bs-target="#approvalModel" title="Cleared" data-id=\'' . json_encode($data->id) . '\'> <i class="ti ti-check"></i></button>';
                            }
                        }
                        return $buttons;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('pages.admin.cheque.index');
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
            //throw $th;
        }
    }

    public function authorized_signatories(){
        try {
            return AuthorizedSignatory::all();
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage(), 'status' => false], 500);
        }
    }
}
