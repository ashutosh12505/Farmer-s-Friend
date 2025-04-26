<!DOCTYPE html>
<html>
<head>
    <title>🌾 Farmer's Friend</title>
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
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card h2 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .card p {
            color: #666;
            margin-bottom: 20px;
        }
        .card a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        .card a:hover {
            background-color: #45a049;
        }
        .card-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            color: #4CAF50;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <h1>Farmer's Friend</h1>
    </div>

    <div class="container">
        <div class="card-container">
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-cloud-sun"></i>
                </div>
                <h2>Weather Prediction</h2>
                <p>Get accurate weather forecasts for your location to plan your farming activities.</p>
                <a href="{{ route('prediction') }}">Check Weather</a>
            </div>
            
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h2>Crop Suggestion</h2>
                <p>Get personalized crop recommendations based on your location and weather conditions.</p>
                <a href="{{ route('crop-suggestion') }}">Get Suggestions</a>
            </div>
            
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-bug"></i>
                </div>
                <h2>Pest Alerts</h2>
                <p>Stay informed about potential pest attacks and get preventive measures.</p>
                <a href="{{ route('pest-alerts') }}">View Alerts</a>
            </div>
            
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2>Weather Warnings</h2>
                <p>Receive timely alerts about extreme weather conditions affecting your crops.</p>
                <a href="{{ route('weather-warnings') }}">Check Warnings</a>
            </div>
            
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h2>Farming Precautions</h2>
                <p>Learn about necessary precautions based on current weather and crop conditions.</p>
                <a href="{{ route('farming-precautions') }}">View Precautions</a>
            </div>
            
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h2>Market Prices</h2>
                <p>View current and historical crop prices to make informed selling decisions.</p>
                <a href="{{ route('market-prices') }}">View Prices</a>
            </div>
        </div>
    </div>
</body>
</html> 