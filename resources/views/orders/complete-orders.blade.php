@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            @if (session()->has('success'))
                <div class="alert text-white bg-success" role="alert">
                    <div class="iq-alert-text">{{ session('success') }}</div>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="mb-3">Complete Order List</h4>
                </div>
                <div>
                    <a href="{{ route('order.exportData') }}" class="btn btn-warning add-list">Export</a>
                    <a href="{{ route('order.completeOrders') }}" class="btn btn-danger add-list">
                        <i class="fa-solid fa-trash mr-3"></i>Clear Search
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <form action="{{ route('order.completeOrders') }}" method="get">
                <div class="d-flex flex-wrap align-items-center justify-content-between">
                    <div class="form-group row">
                        <label for="row" class="col-sm-3 align-self-center">Row:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="row">
                                <option value="10" @if(request('row') == '10')selected="selected"@endif>10</option>
                                <option value="25" @if(request('row') == '25')selected="selected"@endif>25</option>
                                <option value="50" @if(request('row') == '50')selected="selected"@endif>50</option>
                                <option value="100" @if(request('row') == '100')selected="selected"@endif>100</option>
                            </select>
                        </div>
                    </div>

                   <div class="col-md-5 mb-3">
                    <label class="form-label fw-semibold">Date Range:</label>
                    <div class="d-flex gap-2">
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                        <span class="align-self-center px-2">to</span>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        <button type="submit" class="btn btn-primary ms-2">Filter</button>
                        <a href="{{ route('order.completeOrders') }}" class="btn btn-danger ms-2">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    </div>
                </div>
                    
                    <div class="form-group row">
                        <label class="control-label col-sm-3 align-self-center" for="search">Search:</label>
                        <div class="col-sm-8">
                            <div class="input-group">
                                <input type="text" id="search" class="form-control" name="search" placeholder="Search order" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text bg-primary"><i class="fa-solid fa-magnifying-glass font-size-20"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>No.</th>
                            <th>Invoice No</th>
                            <th>@sortablelink('customer.name', 'name')</th>
                            <th>@sortablelink('order_date', 'order date')</th>
                            <!-- <th>@sortablelink('pay')</th>
                            <th>Payment</th> -->
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @foreach ($orders as $order)
                        <tr>
                            <td>{{ (($orders->currentPage() * 10) - 10) + $loop->iteration  }}</td>
                            <td>{{ $order->invoice_no }}</td>
                            <td>{{ $order->customer->name }}</td>
                            <td>{{ $order->order_date }}</td>
                            <!-- <td>{{ $order->pay }}</td>
                            <td>{{ $order->payment_status }}</td> -->
                            <td>
                                <span class="badge badge-success">{{ $order->order_status }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center list-action">
                                    <a class="btn btn-info mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Details" href="{{ route('order.orderDetails', $order->id) }}">
                                        Details
                                    </a>
                                    <a class="btn btn-success mr-2" data-toggle="tooltip" data-placement="top" title="" data-original-title="Print" href="{{ route('order.invoiceDownload', $order->id) }}">
                                        Print
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        </div>
    </div>
    <!-- Page end  -->
</div>
<script>
    document.getElementById('clear-search').addEventListener('click', function () {
        const form = document.querySelector('form');

        // Kosongkan semua input dan select
        form.querySelectorAll('input, select').forEach(el => {
            if (el.type === 'select-one') {
                el.selectedIndex = 0;
            } else if (el.type === 'date' || el.type === 'text') {
                el.value = '';
            }
        });

        // Submit form ulang agar URL jadi bersih tanpa query
        form.submit();
    });
</script>


@endsection
