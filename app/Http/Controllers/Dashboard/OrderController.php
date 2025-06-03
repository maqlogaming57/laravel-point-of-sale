<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Gloudemans\Shoppingcart\Facades\Cart;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pendingOrders(Request $request)
    {
        $query = Order::query()
            ->with('customer')
            ->where('order_status', 'pending');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('invoice_no', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('customer', function($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $orders = $query->sortable()
                        ->latest()
                        ->paginate($request->input('row', 10))
                        ->appends($request->except('page'));

        return view('orders.pending-orders', compact('orders'));
    }

    public function completeOrders(Request $request)
    {
        $query = Order::query()->where('order_status', 'complete');

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('order_date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Existing search functionality
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('invoice_no', 'LIKE', '%' . $request->search . '%')
                ->orWhereHas('customer', function($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search . '%');
                });
            });
        }

        $orders = $query->sortable()
                        ->latest()  // Order by latest first
                        ->paginate($request->input('row', 10))
                        ->appends($request->except('page'));

        return view('orders.complete-orders', compact('orders'));
    }

    public function stockManage()
    {
        $row = (int) request('row', 10);

        if ($row < 1 || $row > 100) {
            abort(400, 'The per-page parameter must be an integer between 1 and 100.');
        }

        return view('stock.index', [
            'products' => Product::with(['category', 'supplier'])
                ->filter(request(['search']))
                ->sortable()
                ->paginate($row)
                ->appends(request()->query()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeOrder(Request $request)
    {
        $rules = [
            'customer_id' => 'required|numeric',
            'payment_status' => 'required|string',
            'pay' => 'numeric|nullable',
            'due' => 'numeric|nullable',
        ];

        $invoice_no = IdGenerator::generate([
            'table' => 'orders',
            'field' => 'invoice_no',
            'length' => 10,
            'prefix' => 'INV-'
        ]);

        $validatedData = $request->validate($rules);
        $validatedData['order_date'] = Carbon::now()->format('Y-m-d');
        $validatedData['order_status'] = 'pending';
        $validatedData['total_products'] = Cart::count();
        $validatedData['sub_total'] = Cart::subtotal();
        $validatedData['vat'] = Cart::tax();
        $validatedData['invoice_no'] = $invoice_no;
        $validatedData['total'] = Cart::total();
        $validatedData['due'] = Cart::total() - $validatedData['pay'];
        $validatedData['created_at'] = Carbon::now();

        $order_id = Order::insertGetId($validatedData);

        // Create Order Details
        $contents = Cart::content();
        $oDetails = array();

        foreach ($contents as $content) {
            $oDetails['order_id'] = $order_id;
            $oDetails['product_id'] = $content->id;
            $oDetails['quantity'] = $content->qty;
            $oDetails['unitcost'] = $content->price;
            $oDetails['total'] = $content->total;
            $oDetails['created_at'] = Carbon::now();

            OrderDetails::insert($oDetails);
        }

        // Delete Cart Sopping History
        Cart::destroy();

        return Redirect::route('dashboard')->with('success', 'Order has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function orderDetails(Int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
                        ->where('order_id', $order_id)
                        ->orderBy('id', 'DESC')
                        ->get();

        return view('orders.details-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request)
    {
        $order_id = $request->id;

        // Reduce the stock
        $products = OrderDetails::where('order_id', $order_id)->get();

        foreach ($products as $product) {
            Product::where('id', $product->product_id)
                    ->update(['product_store' => DB::raw('product_store-'.$product->quantity)]);
        }

        Order::findOrFail($order_id)->update(['order_status' => 'complete']);

        return Redirect::route('order.pendingOrders')->with('success', 'Order has been completed!');
    }

    public function invoiceDownload(Int $order_id)
    {
        $order = Order::where('id', $order_id)->first();
        $orderDetails = OrderDetails::with('product')
                        ->where('order_id', $order_id)
                        ->orderBy('id', 'DESC')
                        ->get();

        // show data (only for debugging)
        return view('orders.invoice-order', [
            'order' => $order,
            'orderDetails' => $orderDetails,
        ]);
    }

    public function pendingDue(Request $request)
    {
        $query = Order::query()->where('due', '>', '0');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('invoice_no', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('customer', function($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $orders = $query->with('customer')
                        ->sortable()
                        ->latest()
                        ->paginate($request->input('row', 10))
                        ->appends($request->except('page'));

        return view('orders.pending-due', compact('orders'));
    }

    public function orderDueAjax(Int $id)
    {
        $order = Order::findOrFail($id);

        return response()->json($order);
    }

    public function updateDue(Request $request)
    {
        $rules = [
            'order_id' => 'required|numeric',
            'due' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        $order = Order::findOrFail($request->order_id);
        $mainPay = $order->pay;
        $mainDue = $order->due;

        $paid_due = $mainDue - $validatedData['due'];
        $paid_pay = $mainPay + $validatedData['due'];

        Order::findOrFail($request->order_id)->update([
            'due' => $paid_due,
            'pay' => $paid_pay,
        ]);

        return Redirect::route('order.pendingDue')->with('success', 'Due Amount Updated Successfully!');
    }

    public function exportData(Request $request)
    {
        try {
            $orders = Order::query()
                ->with(['customer'])
                ->where('order_status', 'complete');

            // Apply date range filter if exists
            if ($request->filled(['start_date', 'end_date'])) {
                $orders->whereBetween('order_date', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay()
                ]);
            }

            $orders = $orders->get();

            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $sheet->setCellValue('A1', 'No.');
            $sheet->setCellValue('B1', 'Invoice No');
            $sheet->setCellValue('C1', 'Customer');
            $sheet->setCellValue('D1', 'Order Date');
            $sheet->setCellValue('E1', 'Payment');
            $sheet->setCellValue('F1', 'Pay');
            $sheet->setCellValue('G1', 'Status');

            // Fill data
            $row = 2;
            foreach ($orders as $index => $order) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $order->invoice_no);
                $sheet->setCellValue('C' . $row, $order->customer->name);
                $sheet->setCellValue('D' . $row, $order->order_date);
                $sheet->setCellValue('E' . $row, $order->payment_status);
                $sheet->setCellValue('F' . $row, $order->pay);
                $sheet->setCellValue('G' . $row, $order->order_status);
                $row++;
            }

            // Auto size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create file
            $writer = new Xlsx($spreadsheet);
            $fileName = 'complete_orders_' . date('Y-m-d_His') . '.xlsx';
            
            // Save file and force download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export data: ' . $e->getMessage());
        }
    }

    public function rejectOrder(Request $request)
    {
        $order = Order::findOrFail($request->id);
        $order->update([
            'order_status' => 'rejected'
        ]);

        return redirect()
            ->route('order.pendingOrders')
            ->with('success', 'Order has been rejected successfully');
    }

    public function rejectedOrders(Request $request)
    {
        $query = Order::query()
            ->with('customer')
            ->where('order_status', 'rejected');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('invoice_no', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('customer', function($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $orders = $query->sortable()
                        ->latest()
                        ->paginate($request->input('row', 10))
                        ->appends($request->except('page'));

        return view('orders.rejected-orders', compact('orders'));
    }
}
