
<div class="collapse navbar-collapse" id="navbarCollapse">
    <div class="navbar-nav ms-auto py-0 pe-4">
        <a href="{{ route('template.client.index') }}" class="nav-item nav-link @if(request()->routeIs('template.client.index')) active @endif">Home</a>
        <a href="{{ route('template.about') }}" class="nav-item nav-link @if(request()->routeIs('template.about')) active @endif">About</a>
        <a href="{{ route('template.service') }}" class="nav-item nav-link @if(request()->routeIs('template.service')) active @endif">Service</a>
        <a href="{{ route('template.menu') }}" class="nav-item nav-link @if(request()->routeIs('template.menu')) active @endif">Menu</a>
        <div class="nav-item dropdown">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle @if(in_array(request()->path(), ['template/booking', 'template/team', 'template/testimonial'], true)) active @endif"
                data-bs-toggle="dropdown">Pages</a>
            <div class="dropdown-menu m-0">
                <a href="{{ route('template.booking') }}" class="dropdown-item @if(request()->routeIs('template.booking')) active @endif">Booking</a>
                <a href="{{ route('template.team') }}" class="dropdown-item @if(request()->routeIs('template.team')) active @endif">Our Team</a>
                <a href="{{ route('template.testimonial') }}" class="dropdown-item @if(request()->routeIs('template.testimonial')) active @endif">Testimonial</a>
            </div>
        </div>
        <a href="{{ route('template.contact') }}" class="nav-item nav-link @if(request()->routeIs('template.contact')) active @endif">Contact</a>
    </div>
    <a href="javascript:void(0);" class="btn btn-primary py-2 px-4">Book A Table</a>
</div>
