@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4">
        <h2 class="text-blue-500 uppercase tracking-wide font-semibold">
            Popular Games
        </h2>
        <livewire:popular-games lazy />
        <div class="flex flex-col lg:flex-row my-10">
            <div class="recently-reviewed w-full lg:w-3/4 mr-0 lg:mr-32">
                <h2 class="text-blue-500 uppercase tracking-wide font-semi-bold">
                    Recently Reviewed
                </h2>
                <livewire:recently-reviewed lazy />
            </div>
            <div class="most-anticipated lg:w-1/4 mt-12 lg:mt-0">
                <h2 class="text-blue-500 uppercase tracking-wide font-semi-bold">
                    Most Anticipated
                </h2>
                <livewire:most-anticipated lazy />
                <h2 class="text-blue-500 uppercase tracking-wide font-semi-bold mt-12">
                    Coming Soon
                </h2>
                <livewire:coming-soon lazy />
            </div>
        </div>
    </div> <!-- end container -->
@endsection
