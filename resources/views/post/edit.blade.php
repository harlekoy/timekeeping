@extends('layouts.app')

@section('content')
<div class="container min-h-screen flex justify-center">
    <post-editor :post="{{ json_encode($post) }}" />
</div>
@endsection
