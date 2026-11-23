@php($pageTitle = $title ?? '')
<section class="page-header">
    <div class="page-header__bg"
        style="background-image: url({{ asset('assets/images/resources/page-header-img-1.png') }})"></div>
    <div class="container">
        <div class="page-header__inner">
            <h1>{{ $pageTitle }}</h1>
        </div>
    </div>
</section>

@include('partials.page-nav-widget')
