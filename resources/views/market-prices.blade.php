<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌾 Farmer's Friend - Market Prices</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow-x: auto;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .search-container {
            margin: 20px 0;
            text-align: center;
        }
        .search-input {
            width: 300px;
            padding: 10px;
            border: 2px solid #4CAF50;
            border-radius: 4px;
            font-size: 16px;
            outline: none;
        }
        .search-input:focus {
            border-color: #45a049;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }
        .no-results {
            text-align: center;
            color: #666;
            padding: 20px;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            white-space: nowrap;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
            position: sticky;
            top: 0;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background-color: #45a049;
        }
        .price-cell {
            text-align: right;
        }
        .serial-cell {
            text-align: center;
            width: 80px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            text-align: center;
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('home') }}" class="back-button">← Back to Home</a>
        <h1>Crop Prices Over Years (in ₹ per Quintal)</h1>
        
        @if(count($prices) > 0)
            <div class="search-container">
                <input type="text" id="searchInput" class="search-input" placeholder="Search Crop name...">
            </div>
            
            <table id="priceTable">
                <thead>
                    <tr>
                        <th class="serial-cell">Serial No.</th>
                        <th>Crop Name</th>
                        @php
                            // Get all year columns by removing serial_no and crop_name
                            $yearColumns = array_keys($prices[0]);
                            $yearColumns = array_filter($yearColumns, function($key) {
                                return !in_array($key, ['serial_no', 'crop_name']);
                            });
                        @endphp
                        @foreach($yearColumns as $year)
                            <th class="price-cell">{{ $year }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($prices as $price)
                        <tr>
                            <td class="serial-cell">{{ $price['serial_no'] }}</td>
                            <td>{{ $price['crop_name'] }}</td>
                            @foreach($yearColumns as $year)
                                <td class="price-cell">{{ $price[$year] }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div id="noResults" class="no-results" style="display: none;">
                No crops found matching your search.
            </div>
        @else
            <p style="text-align: center; color: #666;">No crop price data available.</p>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('priceTable');
            const noResults = document.getElementById('noResults');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            searchInput.addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                let hasResults = false;

                for (let i = 0; i < rows.length; i++) {
                    const cropName = rows[i].getElementsByTagName('td')[1].textContent.toLowerCase();
                    if (cropName.includes(searchText)) {
                        rows[i].style.display = '';
                        hasResults = true;
                    } else {
                        rows[i].style.display = 'none';
                    }
                }

                if (hasResults) {
                    noResults.style.display = 'none';
                    table.style.display = '';
                } else {
                    noResults.style.display = 'block';
                    table.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 