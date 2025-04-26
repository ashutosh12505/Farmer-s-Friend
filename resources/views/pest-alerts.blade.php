<!DOCTYPE html>
<html>
<head>
    <title>🌾 Farmer's Friend - Pest Alerts</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            display: flex;
            gap: 20px;
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
        .card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        select, button {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #45a049;
        }
        .weather-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .weather-card {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .weather-card h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .weather-card p {
            margin: 0;
            font-size: 1.2em;
            color: #666;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .loading {
            text-align: center;
            padding: 20px;
            display: none;
        }
        .left-section {
            flex: 3;
        }
        .right-section {
            flex: 1;
        }
        .recent-crops {
            margin-top: 20px;
        }
        .recent-crop-item {
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .recent-crop-item:hover {
            background-color: #e9ecef;
        }
        .recent-crop-item h3 {
            margin: 0;
            color: #333;
        }
        .recent-crop-item p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9em;
        }
        .no-recent-crops {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="{{ route('home') }}" class="home-button">← Back to Home</a>
        <h1>Pest Alerts</h1>
    </div>

    <div class="container">
        <div class="left-section">
            <div class="card">
                <h2>Select Your Crop</h2>
                <div class="form-group">
                    <label for="crop">Choose a crop:</label>
                    <select id="crop" name="crop">
                        <option value="">Select a crop...</option>
                        <option value="Bajra">Bajra</option>
                        <option value="Barley">Barley</option>
                        <option value="Cardamom">Cardamom</option>
                        <option value="Chillies">Chillies</option>
                        <option value="Coffee">Coffee</option>
                        <option value="Cotton">Cotton</option>
                        <option value="Groundnut">Groundnut</option>
                        <option value="Maize">Maize</option>
                        <option value="Mustard">Mustard</option>
                        <option value="Onion">Onion</option>
                        <option value="Potato">Potato</option>
                        <option value="Pulses">Pulses</option>
                        <option value="Rice">Rice</option>
                        <option value="Sorghum">Sorghum</option>
                        <option value="Soybean">Soybean</option>
                        <option value="Sugarcane">Sugarcane</option>
                        <option value="Tea">Tea</option>
                        <option value="Tomato">Tomato</option>
                        <option value="Turmeric">Turmeric</option>
                        <option value="Wheat">Wheat</option>
                    </select>
                </div>
                <button onclick="getPestAlerts()">Check Pest Alerts</button>
            </div>

            <div id="weatherInfo" class="card" style="display: none;">
                <h2>Current Weather Conditions</h2>
                <div class="weather-info">
                    <div class="weather-card">
                        <h3>Temperature</h3>
                        <p id="temperature">--°C</p>
                    </div>
                    <div class="weather-card">
                        <h3>Humidity</h3>
                        <p id="humidity">--%</p>
                    </div>
                    <div class="weather-card">
                        <h3>Rainfall (1h)</h3>
                        <p id="rainfall">-- mm</p>
                    </div>
                </div>
            </div>

            <div id="pestAlert" class="card" style="display: none;">
                <h2>Pest Alert</h2>
                <div id="alertMessage" class="alert"></div>
            </div>

            <div id="loading" class="loading">
                <p>Loading weather data and checking for pest risks...</p>
            </div>
        </div>

        <div class="right-section">
            <div class="card">
                <h2>Recently Checked Crops</h2>
                <div id="recentCrops" class="recent-crops">
                    <!-- Recent crops will be displayed here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to save recent crop to local storage
        function saveRecentCrop(crop) {
            let recentCrops = JSON.parse(localStorage.getItem('recentCrops') || '[]');
            
            // Remove if crop already exists
            recentCrops = recentCrops.filter(item => item.crop !== crop);
            
            // Add new crop at the beginning
            recentCrops.unshift({
                crop: crop,
                timestamp: new Date().toISOString()
            });
            
            // Keep only the 5 most recent crops
            recentCrops = recentCrops.slice(0, 5);
            
            localStorage.setItem('recentCrops', JSON.stringify(recentCrops));
            displayRecentCrops();
        }

        // Function to display recent crops
        function displayRecentCrops() {
            const recentCropsContainer = document.getElementById('recentCrops');
            const recentCrops = JSON.parse(localStorage.getItem('recentCrops') || '[]');
            
            if (recentCrops.length === 0) {
                recentCropsContainer.innerHTML = '<div class="no-recent-crops">No recently checked crops</div>';
                return;
            }
            
            recentCropsContainer.innerHTML = recentCrops.map(item => {
                const date = new Date(item.timestamp);
                const timeAgo = getTimeAgo(date);
                return `
                    <div class="recent-crop-item" onclick="selectRecentCrop('${item.crop}')">
                        <h3>${item.crop}</h3>
                        <p>Checked ${timeAgo}</p>
                    </div>
                `;
            }).join('');
        }

        // Function to get time ago string
        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            
            if (seconds < 60) return 'just now';
            
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
            
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
            
            const days = Math.floor(hours / 24);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        }

        // Function to select a recent crop
        function selectRecentCrop(crop) {
            document.getElementById('crop').value = crop;
            getPestAlerts();
        }

        function getPestAlerts() {
            const crop = document.getElementById('crop').value;
            if (!crop) {
                alert('Please select a crop');
                return;
            }

            document.getElementById('loading').style.display = 'block';
            document.getElementById('weatherInfo').style.display = 'none';
            document.getElementById('pestAlert').style.display = 'none';

            // Get current location
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    position => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;

                        // Send request to server
                        fetch('/pest-alerts/get', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                crop: crop,
                                latitude: latitude,
                                longitude: longitude
                            })
                        })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                throw new Error(data.error || 'Failed to fetch pest alerts');
                            }
                            return data;
                        })
                        .then(data => {
                            document.getElementById('loading').style.display = 'none';
                            document.getElementById('weatherInfo').style.display = 'block';
                            document.getElementById('pestAlert').style.display = 'block';

                            // Update weather info
                            document.getElementById('temperature').textContent = `${data.weather.temp}°C`;
                            document.getElementById('humidity').textContent = `${data.weather.humidity}%`;
                            document.getElementById('rainfall').textContent = `${data.weather.rain} mm`;

                            // Update pest alert
                            const alertMessage = document.getElementById('alertMessage');
                            alertMessage.textContent = data.pestAlert;
                            alertMessage.className = 'alert ' + (
                                data.pestAlert.includes('⚠️') ? 'alert-warning' :
                                data.pestAlert.includes('✅') ? 'alert-success' :
                                'alert-info'
                            );

                            // Save to recent crops
                            saveRecentCrop(crop);
                        })
                        .catch(error => {
                            document.getElementById('loading').style.display = 'none';
                            alert(error.message || 'Error fetching pest alerts. Please try again.');
                            console.error('Error:', error);
                        });
                    },
                    error => {
                        document.getElementById('loading').style.display = 'none';
                        let errorMessage = 'Error getting location. ';
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage += 'Please enable location services in your browser settings.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage += 'Location information is unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMessage += 'Location request timed out.';
                                break;
                            default:
                                errorMessage += 'An unknown error occurred.';
                        }
                        alert(errorMessage);
                        console.error('Error:', error);
                    }
                );
            } else {
                document.getElementById('loading').style.display = 'none';
                alert('Geolocation is not supported by your browser');
            }
        }

        // Display recent crops when page loads
        document.addEventListener('DOMContentLoaded', displayRecentCrops);
    </script>
</body>
</html> 