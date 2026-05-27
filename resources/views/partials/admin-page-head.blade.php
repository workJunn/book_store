<section class="section-head section-head--admin">
    <div>
        <h1 class="section-title">{{ $title }}</h1>
    </div>
    @if(($showSearch ?? true))
        @include('partials.admin-search')
    @endif
</section>
