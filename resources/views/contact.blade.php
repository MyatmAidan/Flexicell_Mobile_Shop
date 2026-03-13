@extends('layouts.user')
@section('title', 'Contact Us')
@section('content')
    <!-- SECTION -->
    <div class="section">
        <!-- container -->
        <div class="container" style="min-height: 100vh">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <h2 class="text-center mb-4">Contact Us</h2>
                    <p class="text-muted text-center mb-3">
                        Have questions about a phone model, warranty, or trade‑in? Send us a message.
                    </p>
                    <form>
                        <div class="form-group">
                            <label>Your Name</label>
                            <input type="text" class="form-control" placeholder="Enter your name">
                        </div>
                        <div class="form-group">
                            <label>Your Email</label>
                            <input type="email" class="form-control" placeholder="Enter your email">
                        </div>
                        <div class="form-group">
                            <label>Message</label>
                            <textarea class="form-control" rows="4" placeholder="How can we help?"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /container -->
    </div>
    <!-- /SECTION -->
@endsection
