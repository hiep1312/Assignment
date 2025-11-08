<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <img src="{{ asset("storage/logo-bookio.webp") }}" alt="Logo website" width="45" height="45">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Bookio</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1" wire:ignore>
        @if(request()->routeIs('template.*'))
            @include('admin.template.sidebar', compact('currentRoute'))
        @else
            <li class="menu-item @if(request()->routeIs('admin.dashboard')) active @endif">
                <a href="{{ route('admin.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Analytics">Dashboard</div>
                </a>
            </li>
            <li class="menu-item @if(request()->routeIs('admin.banners.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-image-alt"></i>
                    <div data-i18n="Banner management">Banner Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.banners.create')) active @endif">
                        <a href="{{ route('admin.banners.create') }}" class="menu-link">
                            <div data-i18n="Add new banner">Add New Banner</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.banners.index')) active @endif">
                        <a href="{{ route('admin.banners.index') }}" class="menu-link">
                            <div data-i18n="List banners">List Banners</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.blogs.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-book-content"></i>
                    <div data-i18n="Blog management">Blog Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.blogs.create')) active @endif">
                        <a href="{{ route('admin.blogs.create') }}" class="menu-link">
                            <div data-i18n="Add new blog">Add New Blog</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.blogs.index')) active @endif">
                        <a href="{{ route('admin.blogs.index') }}" class="menu-link">
                            <div data-i18n="List blogs">List Blogs</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.categories.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-category"></i>
                    <div data-i18n="Category management">Category Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.categories.create')) active @endif">
                        <a href="{{ route('admin.categories.create') }}" class="menu-link">
                            <div data-i18n="Add new category">Add New Category</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.categories.index')) active @endif">
                        <a href="{{ route('admin.categories.index') }}" class="menu-link">
                            <div data-i18n="List categories">List Categories</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.comments.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-comment"></i>
                    <div data-i18n="Comment management">Comment Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.comments.create')) active @endif">
                        <a href="{{ route('admin.comments.create') }}" class="menu-link">
                            <div data-i18n="Add new comment">Add New Comment</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.comments.index')) active @endif">
                        <a href="{{ route('admin.comments.index') }}" class="menu-link">
                            <div data-i18n="List comments">List Comments</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.images.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-image"></i>
                    <div data-i18n="Image management">Image Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.images.index')) active @endif">
                        <a href="{{ route('admin.images.index') }}" class="menu-link">
                            <div data-i18n="List images">List Images</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.mails.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-envelope"></i>
                    <div data-i18n="Mail management">Mail Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.mails.create')) active @endif">
                        <a href="{{ route('admin.mails.create') }}" class="menu-link">
                            <div data-i18n="Add new mail">Add New Mail</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.mails.index')) active @endif">
                        <a href="{{ route('admin.mails.index') }}" class="menu-link">
                            <div data-i18n="List mails">List Mails</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.mails.center')) active @endif">
                        <a href="{{ route('admin.mails.center') }}" class="menu-link">
                            <div data-i18n="Mail center">Mail Center</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.notifications.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-bell"></i>
                    <div data-i18n="Notification management">Notification Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.notifications.create')) active @endif">
                        <a href="{{ route('admin.notifications.create') }}" class="menu-link">
                            <div data-i18n="Add new notification">Add New Notification</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.notifications.index')) active @endif">
                        <a href="{{ route('admin.notifications.index') }}" class="menu-link">
                            <div data-i18n="List notifications">List Notifications</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.orders.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-cart"></i>
                    <div data-i18n="Order management">Order Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.orders.index')) active @endif">
                        <a href="{{ route('admin.orders.index') }}" class="menu-link">
                            <div data-i18n="List orders">List Orders</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.products.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-package"></i>
                    <div data-i18n="Product management">Product Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.products.create')) active @endif">
                        <a href="{{ route('admin.products.create') }}" class="menu-link">
                            <div data-i18n="Add new product">Add New Product</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.products.index')) active @endif">
                        <a href="{{ route('admin.products.index') }}" class="menu-link">
                            <div data-i18n="List products">List Products</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.reviews.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-star"></i>
                    <div data-i18n="Review management">Review Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.reviews.index')) active @endif">
                        <a href="{{ route('admin.reviews.index') }}" class="menu-link">
                            <div data-i18n="List reviews">List Reviews</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item @if(request()->routeIs('admin.users.*')) active open @endif">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div data-i18n="User management">User Management</div>
                </a>

                <ul class="menu-sub">
                    <li class="menu-item @if(request()->routeIs('admin.users.create')) active @endif">
                        <a href="{{ route('admin.users.create') }}" class="menu-link">
                            <div data-i18n="Add new user">Add New User</div>
                        </a>
                    </li>
                    <li class="menu-item @if(request()->routeIs('admin.users.index')) active @endif">
                        <a href="{{ route('admin.users.index') }}" class="menu-link">
                            <div data-i18n="List users">List Users</div>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="menu-item">
                <a href="{{ route('template.admin.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-layout"></i>
                    <div data-i18n="Template">Template</div>
                </a>
            </li>
        @endif
    </ul>
</aside>
