<!DOCTYPE html>
<html>
<head>
    <title>🌾 Farmer's Friend - Farming Precautions</title>
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
        .crop-select {
            width: 400px;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .weather-info {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            text-align: center;
        }
        .precautions-card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .precaution-item {
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
        .location-header {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
        }
        .location-header h2 {
            margin: 0;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ route('home') }}" class="home-button">← Back to Home</a>
        <h1>Farming Precautions</h1>
    </div>

    <div class="container">
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div style="text-align: center; margin: 20px 0;">
            <select id="cropSelect" class="crop-select">
                <option value="">Select a crop</option>
                <option value="bajra">Bajra</option>
                <option value="barley">Barley</option>
                <option value="cardamom">Cardamom</option>
                <option value="chillies">Chillies</option>
                <option value="coffee">Coffee</option>
                <option value="cotton">Cotton</option>
                <option value="groundnut">Groundnut</option>
                <option value="maize">Maize</option>
                <option value="mustard">Mustard</option>
                <option value="onion">Onion</option>
                <option value="potato">Potato</option>
                <option value="pulses">Pulses</option>
                <option value="rice">Rice</option>
                <option value="sorghum">Sorghum</option>
                <option value="soybean">Soybean</option>
                <option value="sugarcane">Sugarcane</option>
                <option value="tea">Tea</option>
                <option value="tomato">Tomato</option>
                <option value="turmeric">Turmeric</option>
                <option value="wheat">Wheat</option>
            </select>

            <div style="margin-top: 10px;">
                <button id="currentLocationBtn" class="location-button">Check</button>
            </div>
        </div>

        @if(isset($weather))
            <div class="weather-info">
                <h3>Current Weather in {{ $weather['location'] }}</h3>
                <p>Temperature: {{ $weather['temperature'] }}°C | Humidity: {{ $weather['humidity'] }}%</p>
            </div>
        @endif

        @if(isset($precautions))
            <div class="precautions-card">
                <h3>Precautions for {{ ucfirst($crop) }}</h3>
                @foreach($precautions as $precaution)
                    <div class="precaution-item">
                        {{ $precaution }}
                    </div>
                @endforeach
            </div>
        @endif

        <script>
            document.getElementById('currentLocationBtn').addEventListener('click', function() {
                const button = this;
                const cropSelect = document.getElementById('cropSelect');
                const selectedCrop = cropSelect.value;

                if (!selectedCrop) {
                    alert('Please select a crop first');
                    return;
                }

                button.disabled = true;
                button.textContent = 'Getting location...';

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("farming-precautions.get") }}';
                            
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
                            
                            const cropInput = document.createElement('input');
                            cropInput.type = 'hidden';
                            cropInput.name = 'crop';
                            cropInput.value = selectedCrop;
                            
                            form.appendChild(csrfToken);
                            form.appendChild(latInput);
                            form.appendChild(lonInput);
                            form.appendChild(cropInput);
                            
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
    </div>
</body>
</html> 