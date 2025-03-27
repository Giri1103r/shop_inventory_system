<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background: url('{{ public_image('neologin.jpg') }}') no-repeat left center fixed;
            background-size: cover;
            text-align: center;
            color: #fff;
            animation: fadeIn 1.2s ease-in-out;
        }

        .container {
            background: rgba(250, 249, 249, 0.7);
            padding: 20px;
            border-radius: 20px;
            width: 50%;
            max-width: 600px;
            height: 450px;
            text-align: center;
            margin: auto;
            margin-left: 950px;
            margin-right: 10%;
            margin-top: 8%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .message {
            font-size: 24px;
            margin-bottom: 20px;
            color: #f5f5f5;
        }

        .message img {
            width: 150px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        .code {
            color: #ff6347;
            font-size: 100px;
            text-shadow: 3px 3px 5px rgba(0, 0, 0, 0.8);
            animation: pulse 3s infinite;
        }

        h4 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        h4:hover {
            color: #015758;
        }

        button {
            padding: 12px 25px;
            background: linear-gradient(45deg, #f5645a, #ca2e13);

            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 18px;
            transition: background 0.4s ease, transform 0.3s ease;
        }

        button:hover {
            background: linear-gradient(45deg, #f5645a, #fc6247);
            transform: scale(1.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes pulse {
            0% {
                opacity: 0.8;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.8;
            }
        }

        @media (max-width: 768px) {
            body {
                background-position: center;
            }

            .container {
                width: 90%;
                margin-right: 5%;
            }

            .code {
                font-size: 60px;
            }

            button {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .code {
                font-size: 40px;
            }

            button {
                font-size: 12px;
                padding: 8px 16px;
            }
        }
        
        .warning-message {
            font-size: 18px;
            font-weight: bold;
            color: #D32F2F;
            margin-bottom: 20px;
            background: rgba(255, 235, 59, 0.8);
            padding: 10px;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="message">
            <img id="dark-logo" src="{{ public_image('logo-dark.png') }}" alt="Logo">
            <div class="code" style="margin-top:-12px;">
                @yield('code')
            </div>
            <div class="warning-message">
                We have disabled the Developer Tool in the production environment.<br>
                Please close the Developer Tool and use the application.
            </div>
            <h4>@yield('message')</h4>
            <a href="{{ admin_url('home') }}"><button>Back to Home</button></a>
        </div>
    </div>
</body>

</html>
