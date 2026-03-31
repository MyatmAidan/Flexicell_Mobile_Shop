<div>
    <div class="brand-logo d-flex align-items-center justify-content-between">
        <a href="" class="text-nowrap logo-img">
            <img src="{{ asset('img/logo.png') }}" alt="" />
        </a>
        <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
        </div>
    </div>

    <style>
        /* Sidebar spacing */
        .left-sidebar .sidebar-nav ul .sidebar-item .sidebar-link {
            padding: 8px 15px;
            border-radius: 6px;
        }
        .left-sidebar .sidebar-nav ul .nav-small-cap {
            padding: 12px 15px 4px;
            margin-top: 4px;
        }
        .left-sidebar .sidebar-nav #sidebarnav > .sidebar-item {
            margin-bottom: 0;
        }
        .left-sidebar .sidebar-nav ul .sidebar-item .first-level .sidebar-item .sidebar-link {
            padding: 6px 15px 6px 35px;
        }

        /* Dropdown arrow */
        .sidebar-link.has-arrow {
            position: relative;
        }
        .sidebar-link.has-arrow::after {
            content: '';
            display: inline-block;
            width: 7px;
            height: 7px;
            border-style: solid;
            border-width: 0 1.5px 1.5px 0;
            border-color: currentColor;
            transform: rotate(45deg);
            position: absolute;
            right: 15px;
            top: 50%;
            margin-top: -5px;
            transition: transform .3s ease;
        }
        .sidebar-item.selected > .sidebar-link.has-arrow::after,
        .sidebar-link.has-arrow.active::after {
            transform: rotate(-135deg);
            margin-top: -2px;
        }

        /* Smooth dropdown animation */
        .left-sidebar .sidebar-nav .first-level.collapse {
            display: block !important;
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transition: max-height .5s ease, opacity .25s ease, padding .25s ease;
            padding: 0;
        }
        .left-sidebar .sidebar-nav .first-level.collapse.in {
            max-height: 500px;
            opacity: 1;
            padding: 4px 0;
        }

        /* Reduced border-radius for buttons globally */
        .btn {
            border-radius: 4px !important;
        }
        .btn-group > .btn:first-child {
            border-top-left-radius: 4px !important;
            border-bottom-left-radius: 4px !important;
        }
        .btn-group > .btn:last-child {
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
        }
        .btn-sm {
            border-radius: 3px !important;
        }
        .btn-lg {
            border-radius: 5px !important;
        }
    </style>

    <!-- Sidebar navigation-->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
        <ul id="sidebarnav">

            {{-- ── Home ── --}}
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                    <span><iconify-icon icon="solar:home-smile-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('admin/direct-sale*') ? 'active' : '' }}" href="{{ route('admin.direct_sale.index') }}" aria-expanded="false">
                    <span><iconify-icon icon="solar:shop-2-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Direct Sale</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('admin/trade-in-sale*') ? 'active' : '' }}" href="{{ route('admin.trade_in.index') }}" aria-expanded="false">
                    <span><iconify-icon icon="solar:transfer-horizontal-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Trade-In Sale</span>
                </a>
            </li>

            {{-- ── Catalog (dropdown) ── --}}
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                <span class="hide-menu">Catalog</span>
            </li>
            <li class="sidebar-item {{ request()->is('admin/phone_model*') || request()->is('admin/product*') || request()->is('admin/device*') ? 'selected' : '' }}">
                <a class="sidebar-link has-arrow {{ request()->is('admin/phone_model*') || request()->is('admin/product*') || request()->is('admin/device*') ? 'active' : '' }}" href="javascript:void(0)" aria-expanded="false">
                    <span><iconify-icon icon="solar:box-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Inventory</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level {{ request()->is('admin/phone_model*') || request()->is('admin/product*') || request()->is('admin/device*') ? 'in' : '' }}">
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/phone_model*') ? 'active' : '' }}" href="{{ route('admin.phone_model.index') }}">
                            <span class="hide-menu">Phone Models</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/product*') ? 'active' : '' }}" href="{{ route('admin.product.index') }}">
                            <span class="hide-menu">Products</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/device*') ? 'active' : '' }}" href="{{ route('admin.device.index') }}">
                            <span class="hide-menu">Devices</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item {{ request()->is('admin/brand*') || request()->is('admin/category*') ? 'selected' : '' }}">
                <a class="sidebar-link has-arrow {{ request()->is('admin/brand*') || request()->is('admin/category*') ? 'active' : '' }}" href="javascript:void(0)" aria-expanded="false">
                    <span><iconify-icon icon="solar:tag-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Attributes</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level {{ request()->is('admin/brand*') || request()->is('admin/category*') ? 'in' : '' }}">
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/brand*') ? 'active' : '' }}" href="{{ route('admin.brand.index') }}">
                            <span class="hide-menu">Brands</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/category*') ? 'active' : '' }}" href="{{ route('admin.category.index') }}">
                            <span class="hide-menu">Categories</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ── Sales & Finance (dropdown) ── --}}
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                <span class="hide-menu">Sales & Finance</span>
            </li>
            <li class="sidebar-item {{ request()->is('admin/order*') || request()->is('admin/trade-in*') ? 'selected' : '' }}">
                <a class="sidebar-link has-arrow {{ request()->is('admin/order*') || request()->is('admin/trade-in*') ? 'active' : '' }}" href="javascript:void(0)" aria-expanded="false">
                    <span><iconify-icon icon="solar:cart-large-minimalistic-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Orders</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level {{ request()->is('admin/order*') || request()->is('admin/trade-in*') ? 'in' : '' }}">
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/order*') ? 'active' : '' }}" href="{{ route('admin.order.index') }}">
                            <span class="hide-menu">Order List</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/trade-in*') ? 'active' : '' }}" href="#">
                            <span class="hide-menu">Trade In</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item {{ request()->is('admin/installment*') || request()->is('admin/warranty-detail*') ? 'selected' : '' }}">
                <a class="sidebar-link has-arrow {{ request()->is('admin/installment*') || request()->is('admin/warranty-detail*') ? 'active' : '' }}" href="javascript:void(0)" aria-expanded="false">
                    <span><iconify-icon icon="solar:bill-list-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Finance</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level {{ request()->is('admin/installment*') || request()->is('admin/warranty-detail*') ? 'in' : '' }}">
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/installment') ? 'active' : '' }}" href="{{ route('admin.installment.index') }}">
                            <span class="hide-menu">Installment</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/installment_rate*') ? 'active' : '' }}" href="{{ route('admin.installment_rate.index') }}">
                            <span class="hide-menu">Installment Plan</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/warranty-detail*') ? 'active' : '' }}" href="{{ route('admin.warranty_detail.index') }}">
                            <span class="hide-menu">Warranty Details</span>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- ── User Management (dropdown) ── --}}
            <li class="nav-small-cap">
                <i class="ti ti-dots nav-small-cap-icon fs-6"></i>
                <span class="hide-menu">Administration</span>
            </li>
            <li class="sidebar-item {{ request()->is('admin/user*') || request()->is('admin/role*') ? 'selected' : '' }}">
                <a class="sidebar-link has-arrow {{ request()->is('admin/user*') || request()->is('admin/role*') ? 'active' : '' }}" href="javascript:void(0)" aria-expanded="false">
                    <span><iconify-icon icon="solar:users-group-rounded-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">User Management</span>
                </a>
                <ul aria-expanded="false" class="collapse first-level {{ request()->is('admin/user*') || request()->is('admin/role*') ? 'in' : '' }}">
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/user*') ? 'active' : '' }}" href="{{ route('admin.user.index') }}">
                            <span class="hide-menu">Users</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link {{ request()->is('admin/role*') ? 'active' : '' }}" href="{{ route('admin.role.index') }}">
                            <span class="hide-menu">Roles</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->is('admin/blog*') ? 'active' : '' }}" href="{{ route('admin.blogs.index') }}" aria-expanded="false">
                    <span><iconify-icon icon="solar:document-text-bold-duotone" class="fs-6"></iconify-icon></span>
                    <span class="hide-menu">Blogs</span>
                </a>
            </li>

        </ul>
    </nav>
    <!-- End Sidebar navigation -->
</div>
