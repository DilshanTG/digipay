<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ProofController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $payments = Payment::with('merchant')
            ->where('status', 'SUCCESS')
            ->when($search, function($query) use ($search) {
                return $query->where('payhere_ref', 'like', "%{$search}%")
                             ->orWhere('order_id', 'like', "%{$search}%")
                             ->orWhere('customer_email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        return view('admin.proofs.index', compact('payments', 'search'));
    }

    public function invoice(Payment $payment)
    {
        return view('admin.proofs.invoice', compact('payment'));
    }

    public function downloadInvoice(Request $request, Payment $payment)
    {
        $description = $request->input('description', $payment->real_description ?? 'Creative Design Services');
        $subDescription = $request->input('sub_description', 'Service Component: Digital Transformation');
        
        $pdf = Pdf::loadView('admin.proofs.invoice_pdf', [
            'payment' => $payment,
            'description' => $description,
            'subDescription' => $subDescription
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Invoice-{$payment->order_id}.pdf");
    }

    public function email(Payment $payment)
    {
        $deliveryDate = $payment->created_at->addHours(26);
        return view('admin.proofs.email', compact('payment', 'deliveryDate'));
    }
}
