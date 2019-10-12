@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    <h2 class="text-2xl font-normal mb-6">Your Attendance</h2>
    <div class="overflow-hidden overflow-x-auto bg-white rounded-lg shadow-sm mb-3">
        <attendances />
    </div>
</div>
@endsection
