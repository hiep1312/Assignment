@assets
    @vite('resources/css/review-section.css')
@endassets

@if($isPlaceholder)
    <div {{ $attributes->class(['pdp-reviews-section placeholder-glow']) }}>
        <div class="pdp-reviews-header {{ $headerClass }}">
            <div class="row gap-3 gap-md-0 align-items-center">
                <div class="col-md-4">
                    <div class="pdp-reviews-summary-box">
                        <div class="pdp-average-rating-large"><span class="placeholder" style="width: 68px;"></span></div>
                        <div class="d-flex justify-content-center align-items-center gap-1 pdp-stars-large mb-2">
                            <span class="pdp-star placeholder" style="width: 20px;"></span>
                            <span class="pdp-star placeholder" style="width: 20px;"></span>
                            <span class="pdp-star placeholder" style="width: 20px;"></span>
                            <span class="pdp-star placeholder" style="width: 20px;"></span>
                            <span class="pdp-star placeholder" style="width: 20px;"></span>
                        </div>
                        <p class="pdp-review-count">Based on <strong><span class="placeholder" style="width: 28px;"></span> reviews</strong></p>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="pdp-rating-breakdown">
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">5<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar placeholder" style="min-height: 0">
                                <div class="pdp-progress" style="width: 65%"></div>
                            </div>
                            <span class="pdp-rating-count"><span class="placeholder col-11"></span></span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">4<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar placeholder" style="min-height: 0">
                                <div class="pdp-progress" style="width: 20%"></div>
                            </div>
                            <span class="pdp-rating-count"><span class="placeholder col-11"></span></span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">3<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar placeholder" style="min-height: 0">
                                <div class="pdp-progress" style="width: 10%"></div>
                            </div>
                            <span class="pdp-rating-count"><span class="placeholder col-11"></span></span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">2<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar placeholder" style="min-height: 0">
                                <div class="pdp-progress" style="width: 3%"></div>
                            </div>
                            <span class="pdp-rating-count"><span class="placeholder col-11"></span></span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">1<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar placeholder" style="min-height: 0">
                                <div class="pdp-progress" style="width: 2%"></div>
                            </div>
                            <span class="pdp-rating-count"><span class="placeholder col-11"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            @isset($actionButton)
                <button {{ $actionButton->attributes->class(['pdp-btn-write-review']) }}>
                    {{ $actionButton }}
                </button>
            @endisset

            {{ $slot }}

            @isset($loadMoreButton)
                <div class="text-center {{ $loadMoreButton->attributes->get('wrapper-class') }}">
                    <button {{ $loadMoreButton->attributes->class(['pdp-btn-load-more'])->except('wrapper-class') }}>
                        <i class="fas fa-chevron-down"></i> Load more reviews
                    </button>
                </div>
            @endisset
        </div>
    </div>
@else
    <div {{ $attributes->class(['pdp-reviews-section']) }}>
        <div class="pdp-reviews-header {{ $headerClass }}">
            <div class="row gap-3 gap-md-0 align-items-center">
                <div class="col-md-4">
                    <div class="pdp-reviews-summary-box">
                        @php
                            [$reviewScore, $fullStars, $halfStar, $emptyStars] = parseRatingStars($avgRating);
                        @endphp
                        <div class="pdp-average-rating-large">{{ $reviewScore }}</div>
                        <div class="pdp-stars-large mb-2">
                            {!!
                                str_repeat('<span class="pdp-star"><i class="fas fa-star"></i></span>', $fullStars) .
                                str_repeat('<span class="pdp-star"><i class="fas fa-star-half-alt"></i></span>', $halfStar) .
                                str_repeat('<span class="pdp-star-empty"><i class="fas fa-star"></i></span>', $emptyStars)
                            !!}
                        </div>
                        <p class="pdp-review-count">Based on <strong>{{ formatNumberCompact($totalReviews) }} reviews</strong></p>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="pdp-rating-breakdown">
                        @php
                            $fiveStarCount = $starCounts[5] ?? 0;
                            $fourStarCount = $starCounts[4] ?? 0;
                            $threeStarCount = $starCounts[3] ?? 0;
                            $twoStarCount = $starCounts[2] ?? 0;
                            $oneStarCount = $starCounts[1] ?? 0;
                        @endphp
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">5<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar">
                                <div class="pdp-progress" style="width: {{ round(($fiveStarCount / ($totalReviews ?: 1)) * 100) }}%"></div>
                            </div>
                            <span class="pdp-rating-count">{{ $fiveStarCount }}</span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">4<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar">
                                <div class="pdp-progress" style="width: {{ round(($fourStarCount / ($totalReviews ?: 1)) * 100) }}%"></div>
                            </div>
                            <span class="pdp-rating-count">{{ $fourStarCount }}</span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">3<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar">
                                <div class="pdp-progress" style="width: {{ round(($threeStarCount / ($totalReviews ?: 1)) * 100) }}%"></div>
                            </div>
                            <span class="pdp-rating-count">{{ $threeStarCount }}</span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">2<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar">
                                <div class="pdp-progress" style="width: {{ round(($twoStarCount / ($totalReviews ?: 1)) * 100) }}%"></div>
                            </div>
                            <span class="pdp-rating-count">{{ $twoStarCount }}</span>
                        </div>
                        <div class="pdp-rating-row">
                            <span class="pdp-rating-label">1<i class="fas fa-star pdp-star-icon-small"></i></span>
                            <div class="pdp-progress-bar">
                                <div class="pdp-progress" style="width: {{ round(($oneStarCount / ($totalReviews ?: 1)) * 100) }}%"></div>
                            </div>
                            <span class="pdp-rating-count">{{ $oneStarCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @isset($actionButton)
            <button {{ $actionButton->attributes->class(['pdp-btn-write-review']) }}>
                {{ $actionButton }}
            </button>
        @endisset

        {{ $slot }}

        @isset($loadMoreButton)
            <div class="text-center {{ $loadMoreButton->attributes->get('wrapper-class') }}">
                <button {{ $loadMoreButton->attributes->class(['pdp-btn-load-more'])->except('wrapper-class') }}>
                    <i class="fas fa-chevron-down"></i> Load more reviews
                </button>
            </div>
        @endisset
    </div>
@endif
