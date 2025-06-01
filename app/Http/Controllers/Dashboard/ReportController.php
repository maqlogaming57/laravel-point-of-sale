<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()
            ->with(['customer'])
            ->where('order_status', 'complete');

        // Date range filter
        if ($request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('order_date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Search functionality
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('invoice_no', 'LIKE', '%' . $request->search . '%')
                ->orWhereHas('customer', function($q) use ($request) {
                    $q->where('name', 'LIKE', '%' . $request->search . '%');
                });
            });
        }

        $orders = $query->latest()
                       ->paginate($request->input('row', 10))
                       ->appends($request->except('page'));

        return view('reports.index', compact('orders'));
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
            $sheet->setCellValue('G1', 'Total');

            // Fill data
            $row = 2;
            foreach ($orders as $index => $order) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $order->invoice_no);
                $sheet->setCellValue('C' . $row, $order->customer->name);
                $sheet->setCellValue('D' . $row, $order->order_date);
                $sheet->setCellValue('E' . $row, $order->payment_status);
                $sheet->setCellValue('F' . $row, $order->pay);
                $sheet->setCellValue('G' . $row, $order->total);
                $row++;
            }

            // Auto size columns
            foreach (range('A', 'G') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Create file
            $writer = new Xlsx($spreadsheet);
            $fileName = 'sales_report_' . date('Y-m-d_His') . '.xlsx';
            
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
}