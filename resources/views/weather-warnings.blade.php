<!DOCTYPE html>
<html>
<head>
    <title>Weather Warnings - Farmers Friend</title>
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
        .warning-card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            padding: 20px;
        }
        .warning-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .warning-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }
        .warning-meta {
            color: #666;
            font-size: 0.9em;
        }
        .warning-content {
            margin-top: 15px;
        }
        .warning-section {
            margin-bottom: 15px;
        }
        .warning-section-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .warning-section-content {
            color: #666;
            line-height: 1.5;
        }
        .no-warnings {
            text-align: center;
            padding: 40px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
        <h1>Weather Warnings</h1>
    </div>

    <div class="container">
        @if(session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <div style="text-align: center; margin: 20px 0;">
            <button id="currentLocationBtn" class="location-button">Check for current location</button>
        </div>

        @if(isset($location))
            <div class="location-header">
                <h2>Weather Warnings for {{ $location }}</h2>
            </div>
        @endif

        @if(isset($warnings) && count($warnings) > 0)
            @foreach($warnings as $warning)
                <div class="warning-card">
                    <div class="warning-header">
                        <div class="warning-title">{{ $warning['headline'] }}</div>
                        <div class="warning-meta">
                            Severity: {{ $warning['severity'] }} | 
                            Urgency: {{ $warning['urgency'] }}
                        </div>
                    </div>
                    <div class="warning-content">
                        <div class="warning-section">
                            <div class="warning-section-title">Areas Affected:</div>
                            <div class="warning-section-content">{{ $warning['areas'] }}</div>
                        </div>
                        <div class="warning-section">
                            <div class="warning-section-title">Description:</div>
                            <div class="warning-section-content">{{ $warning['description'] }}</div>
                        </div>
                        <div class="warning-section">
                            <div class="warning-section-title">Instructions:</div>
                            <div class="warning-section-content">{{ $warning['instruction'] }}</div>
                        </div>
                        <div class="warning-section">
                            <div class="warning-section-title">Timing:</div>
                            <div class="warning-section-content">
                                Effective: {{ $warning['effective'] }}<br>
                                Expires: {{ $warning['expires'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @elseif(isset($warnings))
            <div class="no-warnings">
                <h3>No active weather warnings for your location</h3>
                <p>There are currently no weather warnings or alerts for your area.</p>
            </div>
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
                            form.action = '{{ route("weather-warnings.get") }}';
                            
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
    </div>
</body>
</html> 