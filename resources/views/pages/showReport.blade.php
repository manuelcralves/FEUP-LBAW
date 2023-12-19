@extends('layouts.app')

@section('title', 'Show Reports')
@section('content')
    <h1>Reports for Auction: {{ $auction->title }}</h1>

    @if(count($reports) > 0)
        <ul>
            @foreach($reports as $report)
                <li>
                    Reported by: {{ $report->user->username }}
                    Reason: {{ $report->reason }}
                    Date: {{ $report->created_at->format('Y-m-d H:i:s') }}
                </li>
            @endforeach
        </ul>
    @else
        <p>No reports for this auction.</p>
    @endif
@endsection