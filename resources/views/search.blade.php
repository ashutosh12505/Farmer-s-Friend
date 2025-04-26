<!DOCTYPE html>
<html>
<head>
    <title>🌾 Farmer's Friend - Weather Prediction</title>
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
            padding: 10px;
            text-align: center;
            position: relative;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .input-form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .input-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .input-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            height: 40px;
            min-width: 120px;
        }
        .input-form button:hover {
            background-color: #45a049;
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
        .weather-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .weather-table th, .weather-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .weather-table th {
            background-color: #4CAF50;
            color: white;
        }
        .weather-table .rain-probability {
            text-align: center;
        }
        .weather-table tr:hover {
            background-color: #f5f5f5;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .city-header {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }
        .city-header h2 {
            margin: 0;
            font-size: 1.5em;
        }
        .condition-icon {
            display: inline-block;
            width: 24px;
            height: 24px;
            margin-right: 5px;
            vertical-align: middle;
        }
        .condition-text {
            vertical-align: middle;
        }
        .location-button {
            background-color: #2196F3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .location-button:hover {
            background-color: #1976D2;
        }
        .location-button:disabled {
            background-color: #BDBDBD;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ route('home') }}" class="home-button">← Back to Home</a>
        <h1>Weather Prediction</h1>
    </div>

    <div class="container">
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div class="input-form" style="max-width: 600px; margin: 0 auto;">
            <form action="{{ route('prediction.search') }}" method="POST" style="text-align: center;">
                @csrf
                <input type="text" name="user_input" placeholder="Enter city name..." required style="width: 400px; margin-bottom: 10px;">
                <div style="display: flex; gap: 10px; justify-content: center;">
                    <button type="submit">Get Weather</button>
                    <button type="button" id="currentLocationBtn" class="location-button">Check for current location</button>
                </div>
            </form>
        </div>

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
                            form.action = '{{ route("prediction.current-location") }}';
                            
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
                            button.textContent = 'Check for current location';
                            alert('Error getting location: ' + error.message);
                        }
                    );
                } else {
                    button.disabled = false;
                    button.textContent = 'Check for current location';
                    alert('Geolocation is not supported by your browser');
                }
            });
        </script>

        @if(isset($weatherData))
            <div class="city-header">
                <h2>Weather prediction for {{ $weatherData['city_name'] === 'Current Location' ? 'Current Location' : ucfirst($weatherData['city_name']) }}</h2>
            </div>
            <table class="weather-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Conditions</th>
                        <th>Temperature</th>
                        <th>Humidity</th>
                        <th>Wind Speed</th>
                        <th>Rain Probability</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $weatherData['current']['day_name'] }}<br>{{ $weatherData['current']['date'] }}</td>
                        <td>
                            <span class="condition-text">{{ ucfirst($weatherData['current']['condition'] ?? 'Clear') }}</span>
                        </td>
                        <td>{{ $weatherData['current']['temperature'] }}°C</td>
                        <td>{{ $weatherData['current']['humidity'] }}%</td>
                        <td>{{ $weatherData['current']['wind_speed'] }} km/h</td>
                        <td>{{ $weatherData['current']['rain_probability'] }}%</td>
                    </tr>
                    @foreach($weatherData['forecast'] as $forecast)
                        <tr>
                            <td>{{ $forecast['day_name'] }}<br>{{ $forecast['date'] }}</td>
                            <td>
                                <span class="condition-text">{{ ucfirst($forecast['condition'] ?? 'Clear') }}</span>
                            </td>
                            <td>{{ $forecast['temperature'] }}°C</td>
                            <td>{{ $forecast['humidity'] }}%</td>
                            <td>{{ $forecast['wind_speed'] }} km/h</td>
                            <td>{{ $forecast['rain_probability'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html> 