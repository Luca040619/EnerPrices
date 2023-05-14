<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prototipo applicazione 'Ener Prices'">
    <title>Ener Prices - Homepage</title>
    <link rel="icon" type="image/gif" href="Icons/Logo Progetto Gpoi V2.png">
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php
    session_start();

    $nome = $_SESSION['nome'];
    $cognome = $_SESSION['cognome'];
    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
    $premium = $_SESSION['premium'];

    if ($username == '') {
        header('Location: login.php');
    }
    ?>

    <header>
        <label class="navbar-icon">
            <input type="checkbox">
        </label>
        <aside class="navbar">
            <nav>
                <div class="navbar-container">
                    <a href="" class="navbar-btn" data-energy="Energy">
                        <img class="sideIcon" src="Icons/bolt icon 2.svg" alt="Energy Icon">
                        <p>Electricity</p>
                    </a>
                </div>
                <div class="navbar-container">
                    <a href="" class="navbar-btn" data-energy="Petrolium">
                        <img class="sideIcon" src="Icons/oil icon.svg" alt="Oil Icon">
                        <p>Petrolium</p>
                    </a>
                </div>
                <div class="navbar-container">
                    <a href="" class="navbar-btn" data-energy="GPL">
                        <img class="sideIcon" src="Icons/gas icon.svg" alt="Gas Icon">
                        <p>GPL</p>
                    </a>
                </div>
                <div id="profileIcon">
                    <a href="profile.php">
                        <img class="sideIcon" src="Icons/person icon.svg" alt="Profile Icon">
                    </a>
                </div>
            </nav>
        </aside>
        <img src="Icons/Logo Progetto Gpoi.png" alt="Logo Ener Prices" id="Homepage-logo">
    </header>
    <section class="pricebox-section">
        <h1 class="title" id="energy-name"></h1>
        <p class="subtitle" id="energy-subtitle"></p>
        <div class="price-container">
            <div class="price-box">
                <h1 class="title">0,00€</h1>
            </div>
        </div>
        <section class="chart-section">
            <canvas id="myChart"></canvas>
        </section>
    </section>
    <section class="table-section">

    </section>
    <script>
        let data;

        var navbarButtons = document.querySelectorAll('.navbar-btn');
        var energyNameElement = document.getElementById('energy-name');
        var energySubtitleElement = document.getElementById('energy-subtitle');
        var navbarCheckbox = document.querySelector('.navbar-icon input');
        var priceElement = document.querySelector('.price-box .title');

        var ctx = document.getElementById('myChart').getContext('2d');
        var chartColors = {
            'energy': 'yellow',
            'petrolium': 'purple',
            'gpl': 'green'
        };

        var monthNames = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];

        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [], // These will be your months
                datasets: [{
                    data: [], // These will be your prices
                    borderColor: 'yellow', // Initial color
                    backgroundColor: 'rgba(255,255,0,0.3)', // Initial color, semi-transparent
                    pointRadius: 5, // Size of the data points
                    pointBackgroundColor: 'yellow', // Initial color
                    pointBorderColor: 'yellow', // Initial color
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255,255,255,0.1)'
                        },
                        ticks: {
                            color: 'white'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(255,255,255,0.1)'
                        },
                        ticks: {
                            color: 'white'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Trend graph of Energy', // Questo sarà il titolo iniziale
                        color: 'yellow', // Colore iniziale
                        font: {
                            size: 20,
                        }
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
            }
        });

        ctx.canvas.style.backgroundColor = '#1B1D29';

        window.onload = async function () {
            data = JSON.parse('<?php require "start_data.php";
            echo getStartData(); ?>');
            console.log(data);

            for (var i = 0; i < navbarButtons.length; i++) {
                navbarButtons[i].addEventListener('click', function (e) {
                    e.preventDefault();
                    var energy = this.getAttribute('data-energy');
                    energyNameElement.textContent = energy;
                    energySubtitleElement.textContent = 'Current Price of ' + energy;
                    navbarCheckbox.checked = false;

                    // Update price based on the energy type
                    var energyLowerCase = energy.toLowerCase();
                    if (data && data.today && data.today[energyLowerCase]) {
                        var unit;
                        var rgbColor;
                        if (energyLowerCase === 'gpl') {
                            unit = ' €/L';
                            rgbColor = 'rgba(0,128,0,0.3)';
                        } else if (energyLowerCase === 'energy') {
                            unit = ' €/Kwh';
                            rgbColor = 'rgba(255,255,0,0.3)';
                        } else if (energyLowerCase === 'petrolium') {
                            unit = ' €/Bar';
                            rgbColor = 'rgba(128,0,128,0.3)';
                        }
                        priceElement.textContent = data.today[energyLowerCase] + unit;
                    }

                    myChart.data.datasets[0].borderColor = chartColors[energyLowerCase];
                    myChart.data.datasets[0].backgroundColor = rgbColor; // Update to the new color
                    myChart.data.datasets[0].pointBackgroundColor = chartColors[energyLowerCase];
                    myChart.data.datasets[0].pointBorderColor = chartColors[energyLowerCase];

                    // Update chart
                    if (data && data.trend) {
                        myChart.data.labels = data.trend.map(function (item) {
                            var splitDate = item.month.split('-');
                            var monthIndex = parseInt(splitDate[0]) - 1; // Subtract 1 because array indices start at 0
                            var year = splitDate[1];
                            return monthNames[monthIndex] + ' ' + year;
                        });
                        myChart.data.datasets[0].data = data.trend.map(function (item) {
                            return item['price' + energy.charAt(0).toUpperCase() + energy.slice(1)];
                        });
                        myChart.data.datasets[0].borderColor = chartColors[energyLowerCase];
                        myChart.options.plugins.title.text = 'Trend graph of ' + energy;
                        myChart.options.plugins.title.color = chartColors[energyLowerCase];
                        myChart.update();
                    }
                });
            }
            document.querySelector('.navbar-btn[data-energy="Energy"]').click();
        }
    </script>
</body>

</html>