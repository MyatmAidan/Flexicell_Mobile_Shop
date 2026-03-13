<div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="" class="text-nowrap logo-img">
            {{-- {{ route('admin.index') }} --}}
            <img src="{{ asset('img/logo.png') }}" alt="" />
        </a>
        <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
        </div>
    </div>
    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
        <ul id="sidebarnav">
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.direct_sale.index') }}" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:shop-2-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Direct Sale</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:shop-2-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Trade In</span>
                </a>
            </li>
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                <span class="hide-menu">Catalog</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.phone_model.index') }}" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:smartphone-2-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Phone Models</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.product.index') }}" aria-expanded="false">    
                    <span>
                        <iconify-icon icon="solar:box-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Products</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.device.index') }}" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:widget-4-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Devices</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.brand.index') }}" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:tag-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Brands</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.category.index') }}" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:tag-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Category</span>
                </a>
            </li>
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                <span class="hide-menu">User Management</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.user.index') }}" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:user-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Users</span>
                </a>
            </li>
            {{-- <li class="sidebar-item">
                <a class="sidebar-link" href="" aria-expanded="false">
                    {{ route('admin.customer.index') }}
                    <span>
                        <iconify-icon icon="solar:user-plus-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Customer</span>
                </a>
            </li> --}}
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:cart-large-minimalistic-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Orders</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Payments</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:card-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Payment Method</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link" href="#" aria-expanded="false">
                    <span>
                        <iconify-icon icon="solar:bill-list-bold-duotone" class="fs-6"></iconify-icon>
                    </span>
                    <span class="hide-menu">Installment Plan</span>
                </a>
            </li>

        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
