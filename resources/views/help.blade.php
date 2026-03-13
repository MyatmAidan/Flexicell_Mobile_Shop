@extends('layouts.user')
@section('title', 'Help')
@section('content')
    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container" style="min-height: 100vh">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <h2 class="text-center mb-4">Help &amp; FAQ</h2>
                    <h4>How do I search for a phone?</h4>
                    <p class="text-muted">
                        Use the search bar at the top to filter by category and model name. Results show phone models
                        and their current selling price.
                    </p>
                    <h4>How is stock calculated?</h4>
                    <p class="text-muted">
                        Stock comes from the number of products and devices available in the Flexicell inventory. When
                        a device is sold in Direct Sale, its status changes to <strong>sold</strong>.
                    </p>
                </div>
            </div>
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->
@endsection
