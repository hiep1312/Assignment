@php
    $activeRoutes = (object)[
        'dashboard' => $currentRoute === 'admin',
        'layouts' => in_array($currentRoute, ['layouts-without-menu', 'layouts-without-navbar', 'layouts-container', 'layouts-fluid', 'layouts-blank'], true),
        'account_settings' => in_array($currentRoute, ['pages-account-settings-account', 'pages-account-settings-notifications', 'pages-account-settings-connections'], true),
        'authentications' => in_array($currentRoute, ['auth-login-basic', 'auth-register-basic', 'auth-forgot-password-basic'], true),
        'misc' => in_array($currentRoute, ['pages-misc-error', 'pages-misc-under-maintenance'], true),
        'cards' => $currentRoute === 'cards-basic',
        'user_interface' => in_array($currentRoute, ['ui-accordion', 'ui-alerts', 'ui-badges', 'ui-buttons', 'ui-carousel', 'ui-collapse', 'ui-dropdowns', 'ui-footer', 'ui-list-groups', 'ui-modals', 'ui-navbar', 'ui-offcanvas', 'ui-pagination-breadcrumbs', 'ui-progress', 'ui-spinners', 'ui-tabs-pills', 'ui-toasts', 'ui-tooltips-popovers', 'ui-typography'], true),
        'extended_ui' => in_array($currentRoute, ['extended-ui-perfect-scrollbar', 'extended-ui-text-divider'], true),
        'boxicons' => $currentRoute === 'icons-boxicons',
        'form_elements' => in_array($currentRoute, ['forms-basic-inputs', 'forms-input-groups'], true),
        'form_layouts' => in_array($currentRoute, ['form-layouts-vertical', 'form-layouts-horizontal'], true),
        'tables' => $currentRoute === 'tables-basic',
    ];

    $activeSubMenu = (object)[
        'layouts' => (object)[
            'without_menu' => $currentRoute === 'layouts-without-menu',
            'without_navbar' => $currentRoute === 'layouts-without-navbar',
            'container' => $currentRoute === 'layouts-container',
            'fluid' => $currentRoute === 'layouts-fluid',
            'blank' => $currentRoute === 'layouts-blank',
        ],
        'account_settings' => (object)[
            'account' => $currentRoute === 'pages-account-settings-account',
            'notifications' => $currentRoute === 'pages-account-settings-notifications',
            'connections' => $currentRoute === 'pages-account-settings-connections',
        ],
        'authentications' => (object)[
            'login' => $currentRoute === 'auth-login-basic',
            'register' => $currentRoute === 'auth-register-basic',
            'forgot_password' => $currentRoute === 'auth-forgot-password-basic',
        ],
        'misc' => (object)[
            'error' => $currentRoute === 'pages-misc-error',
            'under_maintenance' => $currentRoute === 'pages-misc-under-maintenance',
        ],
        'user_interface' => (object)[
            'accordion' => $currentRoute === 'ui-accordion',
            'alerts' => $currentRoute === 'ui-alerts',
            'badges' => $currentRoute === 'ui-badges',
            'buttons' => $currentRoute === 'ui-buttons',
            'carousel' => $currentRoute === 'ui-carousel',
            'collapse' => $currentRoute === 'ui-collapse',
            'dropdowns' => $currentRoute === 'ui-dropdowns',
            'footer' => $currentRoute === 'ui-footer',
            'list_groups' => $currentRoute === 'ui-list-groups',
            'modals' => $currentRoute === 'ui-modals',
            'navbar' => $currentRoute === 'ui-navbar',
            'offcanvas' => $currentRoute === 'ui-offcanvas',
            'pagination_breadcrumbs' => $currentRoute === 'ui-pagination-breadcrumbs',
            'progress' => $currentRoute === 'ui-progress',
            'spinners' => $currentRoute === 'ui-spinners',
            'tabs_pills' => $currentRoute === 'ui-tabs-pills',
            'toasts' => $currentRoute === 'ui-toasts',
            'tooltips_popovers' => $currentRoute === 'ui-tooltips-popovers',
            'typography' => $currentRoute === 'ui-typography',
        ],
        'extended_ui' => (object)[
            'perfect_scrollbar' => $currentRoute === 'extended-ui-perfect-scrollbar',
            'text_divider' => $currentRoute === 'extended-ui-text-divider',
        ],
        'form_elements' => (object)[
            'basic_inputs' => $currentRoute === 'forms-basic-inputs',
            'input_groups' => $currentRoute === 'forms-input-groups',
        ],
        'form_layouts' => (object)[
            'vertical_form' => $currentRoute === 'form-layouts-vertical',
            'horizontal_form' => $currentRoute === 'form-layouts-horizontal',
        ],
    ];
@endphp
<!-- Dashboard -->
<li class="menu-item @if($activeRoutes->dashboard) active @endif">
    <a href="{{ route('template.admin.index') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Analytics">Dashboard</div>
    </a>
</li>

<!-- Layouts -->
<li class="menu-item @if($activeRoutes->layouts) active open @endif">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-layout"></i>
        <div data-i18n="Layouts">Layouts</div>
    </a>

    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->layouts->without_menu) active @endif">
            <a href="{{ route('template.layouts-without-menu') }}" class="menu-link">
                <div data-i18n="Without menu">Without menu</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->layouts->without_navbar) active @endif">
            <a href="{{ route('template.layouts-without-navbar') }}" class="menu-link">
                <div data-i18n="Without navbar">Without navbar</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->layouts->container) active @endif">
            <a href="{{ route('template.layouts-container') }}" class="menu-link">
                <div data-i18n="Container">Container</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->layouts->fluid) active @endif">
            <a href="{{ route('template.layouts-fluid') }}" class="menu-link">
                <div data-i18n="Fluid">Fluid</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->layouts->blank) active @endif">
            <a href="{{ route('template.layouts-blank') }}" class="menu-link">
                <div data-i18n="Blank">Blank</div>
            </a>
        </li>
    </ul>
</li>

<li class="menu-header small text-uppercase">
    <span class="menu-header-text">Pages</span>
</li>

<li class="menu-item @if($activeRoutes->account_settings) active open @endif">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-dock-top"></i>
        <div data-i18n="Account Settings">Account Settings</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->account_settings->account) active @endif">
            <a href="{{ route('template.pages-account-settings-account') }}" class="menu-link">
                <div data-i18n="Account">Account</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->account_settings->notifications) active @endif">
            <a href="{{ route('template.pages-account-settings-notifications') }}" class="menu-link">
                <div data-i18n="Notifications">Notifications</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->account_settings->connections) active @endif">
            <a href="{{ route('template.pages-account-settings-connections') }}" class="menu-link">
                <div data-i18n="Connections">Connections</div>
            </a>
        </li>
    </ul>
</li>
<li class="menu-item @if($activeRoutes->authentications) active open @endif">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-lock-open-alt"></i>
        <div data-i18n="Authentications">Authentications</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->authentications->login) active @endif">
            <a href="{{ route('template.auth-login-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Login</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->authentications->register) active @endif">
            <a href="{{ route('template.auth-register-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Register</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->authentications->forgot_password) active @endif">
            <a href="{{ route('template.auth-forgot-password-basic') }}" class="menu-link" target="_blank">
                <div data-i18n="Basic">Forgot Password</div>
            </a>
        </li>
    </ul>
</li>
<li class="menu-item @if($activeRoutes->misc) active open @endif">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-cube-alt"></i>
        <div data-i18n="Misc">Misc</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->misc->error) active @endif">
            <a href="{{ route('template.pages-misc-error') }}" class="menu-link">
                <div data-i18n="Error">Error</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->misc->under_maintenance) active @endif">
            <a href="{{ route('template.pages-misc-under-maintenance') }}" class="menu-link">
                <div data-i18n="Under Maintenance">Under Maintenance</div>
            </a>
        </li>
    </ul>
</li>
<!-- Components -->
<li class="menu-header small text-uppercase"><span class="menu-header-text">Components</span></li>
<!-- Cards -->
<li class="menu-item @if($activeRoutes->cards) active @endif">
    <a href="{{ route('template.cards-basic') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-collection"></i>
        <div data-i18n="Basic">Cards</div>
    </a>
</li>
<!-- User interface -->
<li class="menu-item @if($activeRoutes->user_interface) active open @endif">
    <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-box"></i>
        <div data-i18n="User interface">User interface</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->user_interface->accordion) active @endif">
            <a href="{{ route('template.ui-accordion') }}" class="menu-link">
                <div data-i18n="Accordion">Accordion</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->alerts) active @endif">
            <a href="{{ route('template.ui-alerts') }}" class="menu-link">
                <div data-i18n="Alerts">Alerts</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->badges) active @endif">
            <a href="{{ route('template.ui-badges') }}" class="menu-link">
                <div data-i18n="Badges">Badges</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->buttons) active @endif">
            <a href="{{ route('template.ui-buttons') }}" class="menu-link">
                <div data-i18n="Buttons">Buttons</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->carousel) active @endif">
            <a href="{{ route('template.ui-carousel') }}" class="menu-link">
                <div data-i18n="Carousel">Carousel</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->collapse) active @endif">
            <a href="{{ route('template.ui-collapse') }}" class="menu-link">
                <div data-i18n="Collapse">Collapse</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->dropdowns) active @endif">
            <a href="{{ route('template.ui-dropdowns') }}" class="menu-link">
                <div data-i18n="Dropdowns">Dropdowns</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->footer) active @endif">
            <a href="{{ route('template.ui-footer') }}" class="menu-link">
                <div data-i18n="Footer">Footer</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->list_groups) active @endif">
            <a href="{{ route('template.ui-list-groups') }}" class="menu-link">
                <div data-i18n="List Groups">List groups</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->modals) active @endif">
            <a href="{{ route('template.ui-modals') }}" class="menu-link">
                <div data-i18n="Modals">Modals</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->navbar) active @endif">
            <a href="{{ route('template.ui-navbar') }}" class="menu-link">
                <div data-i18n="Navbar">Navbar</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->offcanvas) active @endif">
            <a href="{{ route('template.ui-offcanvas') }}" class="menu-link">
                <div data-i18n="Offcanvas">Offcanvas</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->pagination_breadcrumbs) active @endif">
            <a href="{{ route('template.ui-pagination-breadcrumbs') }}" class="menu-link">
                <div data-i18n="Pagination &amp; Breadcrumbs">Pagination &amp; Breadcrumbs</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->progress) active @endif">
            <a href="{{ route('template.ui-progress') }}" class="menu-link">
                <div data-i18n="Progress">Progress</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->spinners) active @endif">
            <a href="{{ route('template.ui-spinners') }}" class="menu-link">
                <div data-i18n="Spinners">Spinners</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->tabs_pills) active @endif">
            <a href="{{ route('template.ui-tabs-pills') }}" class="menu-link">
                <div data-i18n="Tabs &amp; Pills">Tabs &amp; Pills</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->toasts) active @endif">
            <a href="{{ route('template.ui-toasts') }}" class="menu-link">
                <div data-i18n="Toasts">Toasts</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->tooltips_popovers) active @endif">
            <a href="{{ route('template.ui-tooltips-popovers') }}" class="menu-link">
                <div data-i18n="Tooltips & Popovers">Tooltips &amp; popovers</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->user_interface->typography) active @endif">
            <a href="{{ route('template.ui-typography') }}" class="menu-link">
                <div data-i18n="Typography">Typography</div>
            </a>
        </li>
    </ul>
</li>

<!-- Extended components -->
<li class="menu-item @if($activeRoutes->extended_ui) active open @endif">
    <a href="javascript:void(0)" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-copy"></i>
        <div data-i18n="Extended UI">Extended UI</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->extended_ui->perfect_scrollbar) active @endif">
            <a href="{{ route('template.extended-ui-perfect-scrollbar') }}" class="menu-link">
                <div data-i18n="Perfect Scrollbar">Perfect scrollbar</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->extended_ui->text_divider) active @endif">
            <a href="{{ route('template.extended-ui-text-divider') }}" class="menu-link">
                <div data-i18n="Text Divider">Text Divider</div>
            </a>
        </li>
    </ul>
</li>

<li class="menu-item @if($activeRoutes->boxicons) active @endif">
    <a href="{{ route('template.icons-boxicons') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-crown"></i>
        <div data-i18n="Boxicons">Boxicons</div>
    </a>
</li>

<!-- Forms & Tables -->
<li class="menu-header small text-uppercase"><span class="menu-header-text">Forms &amp; Tables</span></li>
<!-- Forms -->
<li class="menu-item @if($activeRoutes->form_elements) active open @endif">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-detail"></i>
        <div data-i18n="Form Elements">Form Elements</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->form_elements->basic_inputs) active @endif">
            <a href="{{ route('template.forms-basic-inputs') }}" class="menu-link">
                <div data-i18n="Basic Inputs">Basic Inputs</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->form_elements->input_groups) active @endif">
            <a href="{{ route('template.forms-input-groups') }}" class="menu-link">
                <div data-i18n="Input groups">Input groups</div>
            </a>
        </li>
    </ul>
</li>
<li class="menu-item @if($activeRoutes->form_layouts) active open @endif">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-detail"></i>
        <div data-i18n="Form Layouts">Form Layouts</div>
    </a>
    <ul class="menu-sub">
        <li class="menu-item @if($activeSubMenu->form_layouts->vertical_form) active @endif">
            <a href="{{ route('template.form-layouts-vertical') }}" class="menu-link">
                <div data-i18n="Vertical Form">Vertical Form</div>
            </a>
        </li>
        <li class="menu-item @if($activeSubMenu->form_layouts->horizontal_form) active @endif">
            <a href="{{ route('template.form-layouts-horizontal') }}" class="menu-link">
                <div data-i18n="Horizontal Form">Horizontal Form</div>
            </a>
        </li>
    </ul>
</li>
<!-- Tables -->
<li class="menu-item @if($activeRoutes->tables) active @endif">
    <a href="{{ route('template.tables-basic') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-table"></i>
        <div data-i18n="Tables">Tables</div>
    </a>
</li>
<!-- Misc -->
<li class="menu-header small text-uppercase"><span class="menu-header-text">Misc</span></li>
<li class="menu-item">
    <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues" target="_blank"
        class="menu-link">
        <i class="menu-icon tf-icons bx bx-support"></i>
        <div data-i18n="Support">Support</div>
    </a>
</li>
<li class="menu-item">
    <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/"
        target="_blank" class="menu-link">
        <i class="menu-icon tf-icons bx bx-file"></i>
        <div data-i18n="Documentation">Documentation</div>
    </a>
</li>
<li class="menu-item">
    <a href="{{ route('admin.dashboard') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cog"></i>
        <div data-i18n="Admin">Admin</div>
    </a>
</li>
