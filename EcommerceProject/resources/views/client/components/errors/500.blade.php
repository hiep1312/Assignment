@assets
    @vite('resources/css/500.css')
@endassets

<div class="err-page">
    <div class="err-container">
        <i class="fas fa-server err-icon"></i>
        <div class="err-code">500</div>
        <h1 class="err-title">Internal Server Error</h1>
        <p class="err-description">
            Sorry! Our server encountered an unexpected issue and could not process your request.
            Please try again in a few moments.
        </p>
        <div class="err-details">
            <i class="fas fa-info-circle" style="margin-right: 1px;"></i>
            Our technical team has been notified. We are working to resolve the issue.
        </div>
        <div style="margin-top: 25px;">
            <a href="{{ route('client.index') }}" class="err-button">
                <i class="fas fa-home"></i> Back to Home
            </a>
            <a href="javascript:window.location.reload();" class="err-button err-button-secondary">
                <i class="fas fa-redo"></i> Reload Page
            </a>
        </div>
    </div>
</div>
