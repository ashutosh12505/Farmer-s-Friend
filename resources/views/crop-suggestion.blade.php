<!DOCTYPE html>
<html>
<head>
    <title>Crop Suggestions - Farmers Friend</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .home-button {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: white;
            color: #4CAF50;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
        }
        .home-button:hover {
            background-color: #f0f0f0;
        }
        .location-button {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .location-button:hover {
            background-color: #1976D2;
        }
        .location-button:disabled {
            background-color: #BDBDBD;
            cursor: not-allowed;
        }
        .weather-info {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .crops-card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .crop-item {
            margin-bottom: 15px;
            padding: 10px;
            border-left: 4px solid #4CAF50;
            background-color: #f9f9f9;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .drought-warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border: 1px solid #ffeeba;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ route('home') }}" class="home-button">← Back to Home</a>
        <h1>Crop Suggestions</h1>
    </div>

    <div class="container">
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="search-form" style="margin-bottom: 20px; text-align: center;">
            <form method="POST" action="{{ route('crop-suggestion.get') }}" style="display: inline-flex; gap: 10px; max-width: 600px; margin: 0 auto;">
                @csrf
                <input type="text" name="city" placeholder="Enter city name" style="width: 400px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Search</button>
            </form>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <span style="color: #666;">OR</span>
        </div>

        <div style="text-align: center; margin: 20px 0;">
            <button id="currentLocationBtn" class="location-button">Get Crop Suggestions for Current Location</button>
        </div>

        @if(isset($weather))
            <div class="weather-info">
                <h3>Current Weather in {{ $weather['location'] }}</h3>
                <p>Temperature: {{ $weather['temperature'] }}°C | Humidity: {{ $weather['humidity'] }}%</p>
            </div>

            @if($isLowHumidity)
                <div class="drought-warning">
                    <h3>Very Low Humidity Detected</h3>
                    <p>Due to low humidity conditions, we recommend the following drought-tolerant crops:</p>
                </div>
                <div class="crops-card">
                    <table>
                        <thead>
                            <tr>
                                <th>Crop Name</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($droughtTolerantCrops as $crop)
                                <tr>
                                    <td>{{ $crop['name'] }}</td>
                                    <td>{{ $crop['notes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="crops-card">
                    <h3>Suggested Crops for Current Conditions</h3>
                    @if(count($suggestedCrops) > 0)
                        <table>
                            <thead>
                                <tr>
                                    <th>Crop Name</th>
                                    <th>Temperature Range (°C)</th>
                                    <th>Humidity Range (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suggestedCrops as $crop)
                                    <tr>
                                        <td>{{ $crop['name'] }}</td>
                                        <td>{{ $crop['tempRange'][0] }} - {{ $crop['tempRange'][1] }}</td>
                                        <td>{{ $crop['humidityRange'][0] }} - {{ $crop['humidityRange'][1] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No crops are suitable for the current weather conditions.</p>
                    @endif
                </div>
            @endif
        @endif

        <script>
            document.getElementById('currentLocationBtn').addEventListener('click', function() {
                const button = this;
                button.disabled = true;
                button.textContent = 'Getting location...';

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("crop-suggestion.get") }}';
                            
                            const csrfToken = document.createElement('input');
                            csrfToken.type = 'hidden';
                            csrfToken.name = '_token';
                            csrfToken.value = '{{ csrf_token() }}';
                            
                            const latInput = document.createElement('input');
                            latInput.type = 'hidden';
                            latInput.name = 'lat';
                            latInput.value = position.coords.latitude;
                            
                            const lonInput = document.createElement('input');
                            lonInput.type = 'hidden';
                            lonInput.name = 'lon';
                            lonInput.value = position.coords.longitude;
                            
                            form.appendChild(csrfToken);
                            form.appendChild(latInput);
                            form.appendChild(lonInput);
                            
                            document.body.appendChild(form);
                            form.submit();
                        },
                        function(error) {
                            button.disabled = false;
                            button.textContent = 'Get Crop Suggestions for Current Location';
                            alert('Error getting location: ' + error.message);
                        }
                    );
                } else {
                    button.disabled = false;
                    button.textContent = 'Get Crop Suggestions for Current Location';
                    alert('Geolocation is not supported by your browser');
                }
            });
        </script>
    </div>
</body>
</html> 