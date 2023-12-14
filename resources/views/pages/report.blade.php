@extends('layouts.app')

@section('title', 'Report Auction')

@section('content')
    <div class="create-auction-container">
        <h2 class="form-title">Report Auction</h2>
        
        <form action="{{ route('report.store') }}" method="POST" class="auction-form">
            @csrf

            <!-- Display Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Auction Fields -->
            <label for="reason">Reason:</label>
            <input type="text" id="reason" name="reason" placeholder="Reason" value="{{ old('reason') }}">

            
            <button type="submit" class="submit-button">Submit Report</button>
            <a href="{{ url('/home') }}" class="cancel-button">Cancel</a>
        </form>
    </div>
@endsection
