@extends('layouts.app')

@section('content')
    <div class="pb-24 lg:pb-0">
        @include('home.stories')
        @include('home.hero-slider')
        @include('home.banner-grid')
        @include('home.category-showcase')
        @include('home.amazing-offer')
        @include('home.promo-duo')
        @include('home.best-sellers')
        @include('home.compare-grid')
        @include('home.about-section')
        @include('home.blog-section')
        @include('home.trust-badges')
    </div>
@endsection
