@assets
    @vite('resources/css/404.css')
@endassets

<div class="err-page">
    <div class="err-container">
        <i class="fas fa-exclamation-circle err-icon"></i>
        <div class="err-code">404</div>
        <h1 class="err-title">Page Not Found</h1>
        <p class="err-description">
            Sorry! The page you’re looking for does not exist or may have been removed.
            Please return to the homepage or use the navigation menu.
        </p>
        <div>
            <a href="{{ route('client.index') }}" class="err-button">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="javascript:window.history.back();" class="err-button err-button-secondary">
                <i class="fas fa-arrow-left"></i> Go Back
            </a>
        </div>
    </div>
</div>
