<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Merchant;
use Illuminate\Http\Request;
use App\Jobs\SyncPaymentToSupabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $payhere;
    protected $paymentService;

    public function __construct(
        \App\Services\PayHereService $payhere,
        \App\Services\PaymentService $paymentService
    )
    {
        $this->payhere = $payhere;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $now = Carbon::now();
        
        $stats = [
            'total_payments' => Payment::count(),
            'successful' => Payment::where('status', 'SUCCESS')->count(),
            'pending' => Payment::where('status', 'PENDING')->count(),
            'failed' => Payment::where('status', 'FAILED')->count(),
            'total_merchants' => Merchant::count(),
            
            // Revenue Stats
            'revenue_today' => Payment::where('status', 'SUCCESS')
                ->whereDate('updated_at', Carbon::today())
                ->sum('amount'),
            
            'revenue_week' => Payment::where('status', 'SUCCESS')
                ->where('updated_at', '>=', Carbon::now()->subDays(7))
                ->sum('amount'),
                
            'revenue_month' => Payment::where('status', 'SUCCESS')
                ->whereMonth('updated_at', $now->month)
                ->whereYear('updated_at', $now->year)
                ->sum('amount'),
                
            'revenue_total' => Payment::where('status', 'SUCCESS')->sum('amount'),
        ];

        // Revenue by Merchant Breakdown
        $merchantBreakdown = DB::table('payments')
            ->join('merchants', 'payments.merchant_id', '=', 'merchants.id')
            ->where('payments.status', 'SUCCESS')
            ->select('merchants.name', DB::raw('SUM(payments.amount) as total_revenue'), DB::raw('COUNT(*) as payment_count'))
            ->groupBy('merchants.id', 'merchants.name')
            ->orderByDesc('total_revenue')
            ->get();

        $recentPayments = Payment::with('merchant')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentPayments', 'merchantBreakdown'));
    }

    public function payments()
    {
        $payments = Payment::with('merchant')->latest()->paginate(20);
        return view('admin.payments', compact('payments'));
    }

    public function syncToSupabase(Request $request)
    {
        $paymentId = $request->payment_id;
        
        if ($paymentId) {
            // Sync single payment
            $payment = Payment::findOrFail($paymentId);
            SyncPaymentToSupabase::dispatch($payment);
            return back()->with('success', 'Payment queued for Supabase sync!');
        } else {
            // Sync all SUCCESS payments
            $payments = Payment::where('status', 'SUCCESS')->get();
            foreach ($payments as $payment) {
                SyncPaymentToSupabase::dispatch($payment);
            }
            return back()->with('success', $payments->count() . ' payments queued for Supabase sync!');
        }
    }

    /**
     * Re-check status from PayHere API and update local DB
     */
    public function checkPayHereStatus($orderId)
    {
        $result = $this->payhere->retrieveOrder($orderId);

        if ($result['status'] === 'success') {
            $paymentList = $result['data'];
            // Usually we take the first successful payment found for this order
            $latestPayment = $paymentList[0]; // status corresponds to "Received" etc.
            
            $payment = Payment::where('order_id', $orderId)->firstOrFail();
            
            // Check if status in PayHere is successful (RECEIVED)
            // Note: PayHere status text: "Received", "Refunded", etc.
            if ($latestPayment['status'] === 'RECEIVED') {
                $this->paymentService->completePayment($payment, $latestPayment['payment_id']);

                return back()->with('success', 'Payment verified and updated from PayHere API!');
            }

            return back()->with('info', "PayHere status is: " . $latestPayment['status']);
        }

        return back()->with('error', 'PayHere Check Failed: ' . ($result['message'] ?? 'Unknown error'));
    }

    /**
     * Reset all payment history (Stats)
     */
    public function resetStats()
    {
        // Truncate the payments table
        Payment::truncate();
        
        return redirect()->route('admin.dashboard')->with('success', 'All statistics and payment history have been reset successfully.');
    }
}
