@extends('layouts.app')

@section('content')
<div class="container">
  {{-- Menu --}}
  {{-- <div class="flex justify-center">
    <ul class="uppercase w-64 flex justify-center">
      <li class="mx-4"><a class="hover:no-underline text-black" href="/">Home</a></li>
      <li class="mx-4"><a class="hover:no-underline text-black" href="/blog">Blog</a></li>
    </ul>
  </div> --}}

  <div class="flex justify-center mb-10">
    <div class="w-2/3 text-center mt-4">
      <h1 class="font-sans font-bold">Always know where you are</h1>
      <p class="font-serif mt-4 -mb-4 text-gray-700">Figured is a complete online livestock crop and production tracking, farm budgeting and forecasting tool that gives you accurate data in one place, in real time.</p>
    </div>
  </div>

  {{-- @if(count($posts)) --}}
  <Posts />
  {{-- @else
  <div class="flex justify-center ">
    <img class="w-1/2 my-12" src="/images/empty-state.svg" alt="No Post">
  </div>
  @endif --}}
</div>
@endsection
