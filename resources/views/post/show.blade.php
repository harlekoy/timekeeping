@extends('layouts.app')

@section('content')
<div class="container min-h-screen flex justify-center">
    <div class="bg-white px-8 -mt-8">
        <h1 class="text-center my-8">{{ $post->title }}</h1>

        <p>
            {!! $post->html !!}
        </p>
    </div>
</div>
@endsection
