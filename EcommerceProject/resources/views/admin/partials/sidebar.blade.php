@php
    $activeRoutes = (object)[
        'dashboard' => $currentRoute === 'template',
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
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('template.index') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="25" viewBox="0 0 25 42" version="1.1" xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs>
                        <path
                            d="M13.7918663,0.358365126 L3.39788168,7.44174259 C0.566865006,9.69408886 -0.379795268,12.4788597 0.557900856,15.7960551 C0.68998853,16.2305145 1.09562888,17.7872135 3.12357076,19.2293357 C3.8146334,19.7207684 5.32369333,20.3834223 7.65075054,21.2172976 L7.59773219,21.2525164 L2.63468769,24.5493413 C0.445452254,26.3002124 0.0884951797,28.5083815 1.56381646,31.1738486 C2.83770406,32.8170431 5.20850219,33.2640127 7.09180128,32.5391577 C8.347334,32.0559211 11.4559176,30.0011079 16.4175519,26.3747182 C18.0338572,24.4997857 18.6973423,22.4544883 18.4080071,20.2388261 C17.963753,17.5346866 16.1776345,15.5799961 13.0496516,14.3747546 L10.9194936,13.4715819 L18.6192054,7.984237 L13.7918663,0.358365126 Z"
                            id="path-1"></path>
                        <path
                            d="M5.47320593,6.00457225 C4.05321814,8.216144 4.36334763,10.0722806 6.40359441,11.5729822 C8.61520715,12.571656 10.0999176,13.2171421 10.8577257,13.5094407 L15.5088241,14.433041 L18.6192054,7.984237 C15.5364148,3.11535317 13.9273018,0.573395879 13.7918663,0.358365126 C13.5790555,0.511491653 10.8061687,2.3935607 5.47320593,6.00457225 Z"
                            id="path-3"></path>
                        <path
                            d="M7.50063644,21.2294429 L12.3234468,23.3159332 C14.1688022,24.7579751 14.397098,26.4880487 13.008334,28.506154 C11.6195701,30.5242593 10.3099883,31.790241 9.07958868,32.3040991 C5.78142938,33.4346997 4.13234973,34 4.13234973,34 C4.13234973,34 2.75489982,33.0538207 2.37032616e-14,31.1614621 C-0.55822714,27.8186216 -0.55822714,26.0572515 -4.05231404e-15,25.8773518 C0.83734071,25.6075023 2.77988457,22.8248993 3.3049379,22.52991 C3.65497346,22.3332504 5.05353963,21.8997614 7.50063644,21.2294429 Z"
                            id="path-4"></path>
                        <path
                            d="M20.6,7.13333333 L25.6,13.8 C26.2627417,14.6836556 26.0836556,15.9372583 25.2,16.6 C24.8538077,16.8596443 24.4327404,17 24,17 L14,17 C12.8954305,17 12,16.1045695 12,15 C12,14.5672596 12.1403557,14.1461923 12.4,13.8 L17.4,7.13333333 C18.0627417,6.24967773 19.3163444,6.07059163 20.2,6.73333333 C20.3516113,6.84704183 20.4862915,6.981722 20.6,7.13333333 Z"
                            id="path-5"></path>
                    </defs>
                    <g id="g-app-brand" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="Brand-Logo" transform="translate(-27.000000, -15.000000)">
                            <g id="Icon" transform="translate(27.000000, 15.000000)">
                                <g id="Mask" transform="translate(0.000000, 8.000000)">
                                    <mask id="mask-2" fill="white">
                                        <use xlink:href="#path-1"></use>
                                    </mask>
                                    <use fill="#696cff" xlink:href="#path-1"></use>
                                    <g id="Path-3" mask="url(#mask-2)">
                                        <use fill="#696cff" xlink:href="#path-3"></use>
                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-3"></use>
                                    </g>
                                    <g id="Path-4" mask="url(#mask-2)">
                                        <use fill="#696cff" xlink:href="#path-4"></use>
                                        <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-4"></use>
                                    </g>
                                </g>
                                <g id="Triangle"
                                    transform="translate(19.000000, 11.000000) rotate(-300.000000) translate(-19.000000, -11.000000) ">
                                    <use fill="#696cff" xlink:href="#path-5"></use>
                                    <use fill-opacity="0.2" fill="#FFFFFF" xlink:href="#path-5"></use>
                                </g>
                            </g>
                        </g>
                    </g>
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2">Sneat</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item @if($activeRoutes->dashboard) active @endif">
            <a href="{{ route('template.index') }}" class="menu-link">
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
    </ul>
</aside>
