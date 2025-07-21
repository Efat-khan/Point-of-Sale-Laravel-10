@php
$id = Auth::user()->id;
$adminData = App\Models\User::find($id);
@endphp
<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <!-- User details -->
        <div class="user-profile text-center mt-3">
            <div class="">
                <img src="{{ (!empty($adminData->profile_image)) ? asset($adminData->profile_image) : asset('upload/no_image.jpg') }}" alt="" class="avatar-md rounded-circle">
            </div>
            <div class="mt-3">
                <h4 class="font-size-16 mb-1">{{ $adminData->name}}</h4>
                <span class="text-muted"><i class="ri-record-circle-line align-middle font-size-14 text-success"></i> Online</span>
            </div>
        </div>
        <!--- Sidebar Menu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
                <li>
                    <a href="{{ url('/dashboard') }}" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <!-- Invoice Menu -->
                @can('invoice-list')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <!-- <i class="ri-file-list-3-line"></i> -->
                        <i class="fa fa-calculator" aria-hidden="true"></i>
                        <span>Invoice</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('invoice.all')}}">Invoice All</a></li>
                        @can('invoice-create')
                        <li><a href="{{route('invoice.add')}}">Invoice Add</a></li>
                        @endcan
                        <!-- <li><a href="{{route('invoice.pending.list')}}">Approval Invoice</a></li> -->
                        <!-- <li><a href="{{route('print.invoice.list')}}">Print Invoice List</a></li> -->
                        <!-- <li><a href="{{route('daily.invoice.report')}}">Invoice Report</a></li> -->
                        <!-- <li><a href="{{route('deliveryzone.invoice.details')}}">Delivery Details</a></li>
                            <li><a href="{{route('deliveryzone.invoice.summary')}}">Delivery Summary</a></li>
                            <li><a href="{{route('deliveryzone.invoice.summary.edit')}}">Delivery Summary Edit</a></li> -->
                    </ul>
                </li>
                @endcan
                <!-- End of Invoice menu -->
                <!-- Purchase Menu -->
                @can('purchase-list')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-shopping-basket-2-fill"></i>
                        <span>Purchase</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('purchase.all')}}">Purchase All</a></li>
                        @can('purchase-create')
                        <li><a href="{{route('purchase.add')}}">Purchase Add</a></li>
                        @endcan
                        @can('supplier-transaction-list')
                        <li><a href="{{route('purchase.supplier_wise_purchese_payment.all')}}">Purchase Payment</a></li>
                        <li><a href="{{route('purchase.supplier.all.transaction')}}">Suppliers Transactions</a></li>
                        @endcan
                        <!-- <li><a href="{{route('purchase.wise.all.report')}}">Purchase Report</a></li> -->
                    </ul>
                </li>
                @endcan
                <!-- Product Menu -->
                @can('product-list')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-price-tag-3-fill"></i>
                        <span>Product</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('product.all')}}">All Product</a></li>
                        @can('product-create')
                        <li><a href="{{route('product.add')}}">Add Product</a></li>
                        @endcan
                        @can('product-price-code-list')
                        <li><a href="{{route('productpricecode.all')}}">Product Price Code</a></li>
                        @endcan
                        {{-- 
                            @can('product-label-list')
                            <li><a href="{{route('productLabelsprint.index')}}">Print Labels</a></li>
                            @endcan
                        --}}
                        @can('brand-list')
                        <li><a href="{{route('brand.add')}}">Brand </a></li>
                        @endcan
                        @can('category-list')
                        <li><a href="{{route('category.add')}}">Category</a></li>
                        @endcan
                        @can('unit-list')
                        <li><a href="{{route('unit.all')}}">Units</a></li>
                        @endcan
                        @can('tax-list')
                        <li><a href="{{route('tax.index')}}">Taxes</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <!-- End of Product menu -->
                <!-- Stock Menu -->
                @can('stock-list')
                <li>
                    <a href="{{route('stock.report')}}" class="has-arrow waves-effect">
                        <i class="fa fa-archive" aria-hidden="true"></i>
                        <span>Manage Stock</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('stock.report')}}">Stock Report</a></li>
                        <!-- <li><a href="{{route('stock.supplier.wise')}}">Supplier / Product wise Report</a></li> -->
                    </ul>
                </li>
                @endcan
                <!--- Customer Menu -->
                @can('customer-list')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-shield-user-fill"></i>
                        <span>Manage Customers</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('customer.all')}}">All Customers</a></li>
                        @can('customer-create')
                        <li><a href="{{route('customer.add')}}">Add Customers</a></li>
                        @endcan
                        @can('customer-transaction-list')
                        <li><a href="{{route('customer.all.transaction')}}">Customers Transactions</a></li>
                        <li><a href="{{route('customer.due_payment.all')}}">Customer Due & Payment</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <!--- End of Customer Menu -->
                <!-- Manage Supplier -->
                @can('supplier-list')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-hotel-fill"></i>
                        <span>Manage Supplier</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('supplier.all')}}">Supplier All</a></li>
                        @can('supplier-create')
                        <li><a href="{{route('supplier.add')}}">Supplier Add</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan



                <!--- SR Menu -->
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-riding-fill"></i>
                        <span>Manage SR</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('sr.add')}}">Add SR</a></li>
                        <li><a href="{{route('sr.all')}}">All SR</a></li>
                        {{-- <li><a href="">SR wise Report</a></li> --}}
                    </ul>
                </li> -->

                <!-- Unit Menu -->
                <!-- <li>
                    <a href="{{route('unit.all')}}" class="has-arrow waves-effect">
                        <i class="fa fa-balance-scale" aria-hidden="true"></i>
                        <span>Manage Units</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('unit.all')}}">All Units</a></li>
                    </ul>
                </li> -->
                <!-- End of Unit menu -->




                <!--- Delevery Menu -->
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-map-pin-fill"></i>
                        <span>Delivery Zone</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('deliveryzone.add')}}">Add Delivery Zone</a></li>
                        <li><a href="{{route('deliveryzone.all')}}">All Delivery Zone</a></li>
                    </ul>
                </li> -->

                <!-- Return Menu -->
                <!-- <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-refund-fill"></i>
                        <span>Manage Return</span>
                    </a>

                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('return.add')}}">Add Return</a></li>
                        <li><a href="{{route('return.all')}}">Return All</a></li>
                        <li><a href="{{route('return.pending')}}">Approval Return</a></li>
                        <li><a href="{{route('daily.return.report')}}">Daily Return Report</a></li>
                    </ul>

                </li> -->
                <!-- End of Return menu -->


                <!--- Manage Users -->
                @can('user-list')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-shield-user-fill"></i>
                        <span>Manage Users</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('users.index')}}">All Users</a></li>
                        @can('user-create')
                        <li><a href="{{route('users.create')}}">Add Users</a></li>
                        @endcan
                        @can('user-list')
                        @endcan
                    </ul>
                </li>
                @endcan
                <!--- Manage Role -->
                @can('role-list')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-shield-user-fill"></i>
                        <span>Manage Role</span>
                    </a>

                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{route('roles.index')}}">All Role</a></li>
                        @can('role-create')
                        <li><a href="{{route('roles.create')}}">Add Role</a></li>
                        @endcan
                    </ul>
                </li>
                @endcan
                <!-- End of Stock menu -->

                <li class="menu-title">Others</li>

                <li>
                    <a href="javascript:void(0);" class="has-arrow waves-effect">
                        <i class="ri-account-circle-line"></i>
                        <span>Authentication</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('admin.profile') }}">Profile</a></li>
                        <li><a href="{{ route('change.password') }}">Change Password</a></li>
                        <li><a href="{{ route('admin.logout') }}">Logout</a></li>
                        {{-- <li><a href="auth-lock-screen.html">Lock Screen</a></li> --}}
                    </ul>
                </li>
                @can('setting')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-settings-4-line"></i>
                        <span>Setting</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('settings.index') }}">SMS API</a></li>
                        {{-- <li><a href="{{ route('change.password') }}">Change Password</a>
                </li> --}}
                {{-- <li><a href="{{ route('admin.logout') }}">Logout</a></li> --}}
                {{-- <li><a href="auth-lock-screen.html">Lock Screen</a></li> --}}
            </ul>

            </li>
            @endcan
            @can('support')
            <li>
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="ri-customer-service-2-fill"></i>
                    <span>Support</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li><a href="">Support Contact</a></li>
                    <li><a href="">Support Ticket</a></li>
                    {{-- <li><a href="">Directory</a></li>
                        <li><a href="">Invoice</a></li> --}}
                </ul>
            </li>
            @endcan
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>