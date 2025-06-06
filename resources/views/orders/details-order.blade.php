@extends('dashboard.body.main')

@section('container')
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Information Order Details</h4>
                    </div>
                </div>

                <div class="card-body">
                    <!-- begin: Show Data -->
                    <!-- <div class="form-group row align-items-center">
                        <div class="col-md-12">
                            <div class="profile-img-edit">
                                <div class="crm-profile-img-edit">
                                    <img class="crm-profile-pic rounded-circle avatar-100" id="image-preview" src="{{ $order->customer->photo ? asset('storage/customers/'.$order->customer->photo) : asset('storage/customers/default.png') }}" alt="profile-pic">
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="row align-items-center">
                        <div class="form-group col-md-12">
                            <label>Customer Name</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->customer->name }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Customer Email</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->customer->email }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Customer Phone</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->customer->phone }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Order Date</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->order_date }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Order Invoice</label>
                            <input class="form-control bg-white" id="buying_date" value="{{ $order->invoice_no }}" readonly/>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Payment Status</label>
                            <input class="form-control bg-white" id="expire_date" value="{{ $order->payment_status }}" readonly />
                        </div>
                        <!-- <div class="form-group col-md-6">
                            <label>Paid Amount</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->pay }}" readonly>
                        </div> -->
                        <div class="form-group col-md-6">
                            <label>Due Amount</label>
                            <input type="text" class="form-control bg-white" value="{{ $order->due }}" readonly>
                        </div>
                    </div>
                    <!-- end: Show Data -->

                    <?php
                    if ($order->order_status == 'pending') {
                        if(auth()->user() && auth()->user()->hasAnyRole(['KepalaGudang', 'SuperAdmin'])) { ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="d-flex align-items-center list-action">
                                        <!-- ACC Pesanan Button with Modal -->
                                        <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#approveModal" 
                                                data-invoice="{{ $order->invoice_no }}" 
                                                data-customer="{{ $order->customer->name }}"
                                                title="Complete">
                                            <i class="fa-solid fa-check mr-2"></i>ACC Pesanan
                                        </button>

                                        <!-- Tolak Pesanan Button with Modal -->
                                        <button type="button" class="btn btn-danger mr-2" data-toggle="modal" data-target="#rejectModal"
                                                data-invoice="{{ $order->invoice_no }}" 
                                                data-customer="{{ $order->customer->name }}"
                                                title="Reject">
                                            <i class="fa-solid fa-times mr-2"></i>Tolak Pesanan
                                        </button>

                                        <a class="btn btn-secondary" href="{{ route('order.pendingOrders') }}">
                                            <i class="fa-solid fa-arrow-left mr-2"></i>Back
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } ?>
                </div>
            </div>
        </div>

        <!-- end: Show Data -->
        <div class="col-lg-12">
            <div class="table-responsive rounded mb-3">
                <table class="table mb-0">
                    <thead class="bg-white text-uppercase">
                        <tr class="ligth ligth-data">
                            <th>No.</th>
                            <th>Photo</th>
                            <th>Product Name</th>
                            <th>Product Code</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total(+vat)</th>
                        </tr>
                    </thead>
                    <tbody class="ligth-body">
                        @foreach ($orderDetails as $item)
                        <tr>
                            <td>{{ $loop->iteration  }}</td>
                            <td>
                                <img class="avatar-60 rounded" src="{{ $item->product->product_image 
                                    ? asset('storage/products/'.$item->product->product_image) 
                                    : asset('assets/images/product/default.webp') }}">
                            </td>
                            <td>{{ $item->product->product_name }}</td>
                            <td>{{ $item->product->product_code }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unitcost }}</td>
                            <td>{{ $item->total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Page end  -->
</div>

<!-- Approve Order Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveModalLabel">
                    <i class="fa-solid fa-check me-2"></i>
                    Confirm Order Approval
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fa-solid fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h5>Are you sure you want to approve this order?</h5>
                <div class="text-muted">
                    <p class="mb-1">
                        <strong>Invoice:</strong> <span id="approveInvoiceNo"></span>
                    </p>
                    <p class="mb-0">
                        <strong>Customer:</strong> <span id="approveCustomerName"></span>
                    </p>
                </div>
                <p class="text-muted mt-2">
                    <small>Once approved, this order will be processed and cannot be reverted.</small>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa-solid fa-times me-1"></i>
                    Cancel
                </button>
                <form id="approveForm" action="{{ route('order.updateStatus') }}" method="POST" style="display: inline;">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $order->id }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-check me-1"></i>
                        Yes, Approve Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Order Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    Confirm Order Rejection
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fa-solid fa-times-circle text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5>Are you sure you want to reject this order?</h5>
                <div class="text-muted">
                    <p class="mb-1">
                        <strong>Invoice:</strong> <span id="rejectInvoiceNo"></span>
                    </p>
                    <p class="mb-0">
                        <strong>Customer:</strong> <span id="rejectCustomerName"></span>
                    </p>
                </div>
                <p class="text-muted mt-2">
                    <small>This action will reject the order and cannot be undone.</small>
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa-solid fa-times me-1"></i>
                    Cancel
                </button>
                <form id="rejectForm" action="{{ route('order.rejectOrder') }}" method="POST" style="display: inline;">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $order->id }}">
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-times me-1"></i>
                        Yes, Reject Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle approve modal
    $('#approveModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var invoiceNo = button.data('invoice');
        var customerName = button.data('customer');
        
        var modal = $(this);
        modal.find('#approveInvoiceNo').text(invoiceNo);
        modal.find('#approveCustomerName').text(customerName);
    });

    // Handle reject modal
    $('#rejectModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var invoiceNo = button.data('invoice');
        var customerName = button.data('customer');
        
        var modal = $(this);
        modal.find('#rejectInvoiceNo').text(invoiceNo);
        modal.find('#rejectCustomerName').text(customerName);
    });
});
</script>

@include('components.preview-img-form')
@endsection