@php
    $helpfulCount = (int) ($review->helpful_count ?? 0);
    $notHelpfulCount = (int) ($review->not_helpful_count ?? 0);
    $userVote = $review->relationLoaded('votes') ? $review->votes->firstWhere('id_users', auth()->id()) : null;
@endphp

<article class="review-card" data-review-card data-review-id="{{ $review->getKey() }}" data-review-user-id="{{ $review->id_users }}">
    <div class="review-card__top">
        <div>
            <div class="review-card__author">{{ $review->user->name ?? 'Пользователь' }}</div>
            <div class="review-card__meta-row">
                <span class="review-card__date">{{ $review->review_date ? $review->review_date->format('d.m.Y H:i') : '' }}</span>
                @if($verifiedBuyerIds->contains($review->id_users))
                    <span class="review-card__badge">Подтвержденная покупка</span>
                @endif
            </div>
        </div>

        <div class="review-card__rating">
            @for($star = 1; $star <= 5; $star++)
                <span class="review-card__star {{ $star <= (int) $review->rating ? 'is-filled' : '' }}">★</span>
            @endfor
        </div>
    </div>

    <div class="review-card__body">
        {{ $review->review_text ?: 'Пользователь оставил только оценку без текста.' }}
    </div>

    <div class="review-feedback">
        <span class="review-feedback__label">Отзыв был полезен?</span>
        <button
            type="button"
            class="review-feedback__button {{ $userVote?->is_helpful === true ? 'is-active' : '' }}"
            data-review-vote
            data-review-vote-url="{{ route('reviews.vote', $review) }}"
            data-vote="helpful"
            @guest data-login-url="{{ route('login') }}" @endguest
        >
            Да <span data-review-helpful-count>{{ $helpfulCount }}</span>
        </button>
        <button
            type="button"
            class="review-feedback__button {{ $userVote?->is_helpful === false ? 'is-active' : '' }}"
            data-review-vote
            data-review-vote-url="{{ route('reviews.vote', $review) }}"
            data-vote="not_helpful"
            @guest data-login-url="{{ route('login') }}" @endguest
        >
            Нет <span data-review-not-helpful-count>{{ $notHelpfulCount }}</span>
        </button>
    </div>
</article>
