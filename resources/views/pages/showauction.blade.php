@extends('layouts.app')

@section('title', 'Auction Details')

@section('content')
    <h2>Auction Details</h2>

    <p><strong>Title:</strong> {{ $auction->title }}</p>
    <p><strong>Current Price:</strong> {{ $auction->current_price }}</p>
    <p><strong>Status:</strong> {{ $auction->status }}</p>
    <p><strong>Description:</strong> {{ $auction->description }}<br></p>
    
    <!-- Table to display bid information -->
    <table id="bidTable">
        <thead>
            <tr>
                <th>Place</th>
                <th>Id</th>
                <th>Rating</th>
                <th>Username</th>
                <th>Bid Amount</th>
            </tr>
        </thead>
        <tbody id="bidTableBody">
            @foreach ($auction->bids as $bid)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $bid->id }}</td>
                    <td>{{ $bid->rating }}</td>
                    <td>{{ $bid->user }}</td>
                    <td>{{ $bid->value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Bid Form -->
    <form id="bidForm" method="POST" action="{{ route('makebid') }}">
        @csrf
        <label for="bid_amount">Bid Amount:</label>
        <input type="number" id="bid_amount" name="bid_amount" min="{{ $auction->current_price }}" step="1" required>
        <button type="submit" class="button">Bid</button>
    </form>

  <!--  <script>
    document.getElementById('bidForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const bidAmount = document.getElementById('bid_amount').value;

        fetch("{{ route('makebid') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({ bid_amount: bidAmount })
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response and update the bid table
            console.log(data);
            // Example: Update the table with data received from the backend
            const tableBody = document.getElementById('bidTableBody');
            // Example row
            const row = `
                <tr>
                    <td>${getOrdinalSuffix(tableBody.getElementsByTagName('tr').length + 1)}</td>
                    <td>${data.rating}</td>
                    <td>${data.username}</td>
                    <td>${data.bid_amount}</td>
                </tr>
            `;
            tableBody.innerHTML += row;

            // Sort the table by bid amount (assuming bid_amount is a number)
            const rows = Array.from(tableBody.getElementsByTagName('tr'));
            rows.sort((a, b) => {
                const amountA = parseFloat(a.getElementsByTagName('td')[3].innerText);
                const amountB = parseFloat(b.getElementsByTagName('td')[3].innerText);
                return amountB - amountA; // Sort in descending order
            });
            tableBody.innerHTML = ''; // Clear the table body
            rows.forEach((row, index) => {
                const placeCell = row.getElementsByTagName('td')[0];
                placeCell.innerText = getOrdinalSuffix(index + 1); // Update the place
                tableBody.appendChild(row); // Append sorted rows
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });

    // Function to get the ordinal suffix (st, nd, rd, th)
    function getOrdinalSuffix(number) {
        const suffixes = ["st", "nd", "rd"];
        const remainder = number % 10;
        return number + (suffixes[remainder - 1] || "th");
    }
</script> -->


@endsection
