@assets
    <style>
        .cop-card {
            background-color: var(--cop-white);
            border-radius: 8px;
            box-shadow: 0 2px 12px var(--cop-shadow);
            padding: 25px;
            margin-bottom: 20px;
        }

        .cop-card-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--cop-text-primary);
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--cop-gray-medium);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cop-card-title i {
            color: var(--cop-primary-orange);
        }
    </style>
@endassets

<div {{ $attributes->class(['cop-card']) }}>
    @isset($title)
        <h2 {{ $title->attributes->class(['cop-card-title'])->except('icon') }}>
            <i class="{{ $title->attributes->get('icon') }}"></i>
            {{ $title }}
        </h2>
    @endisset

    {{ $slot }}
</div>
