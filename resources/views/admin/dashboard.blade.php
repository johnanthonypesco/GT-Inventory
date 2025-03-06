<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://kit.fontawesome.com/aed89df169.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="{{asset ('css/style.css')}}">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <title>Dashboard</title>
</head>
<body class="flex flex-col md:flex-row gap-4 mx-auto">
    <x-admin.navbar/>

    <main class="md:w-full h-full md:ml-[16%]">
        <x-admin.header title="Dashboard" icon="fa-solid fa-gauge" name="John Anthony Pesco" gmail="admin@gmail"/>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-center mt-5">
            <x-admin.dashboardcard title="Total Orders" image="complete.png" count="10"/>
            <x-admin.dashboardcard title="Pending Orders" image="pending.png" count="10"/>
            <x-admin.dashboardcard title="Cancelled Orders" image="cancel.png" count="10"/>
            <x-admin.dashboardcard title="Unread Messages" image="messages.png" count="10"/>
        </div>

        <div class="flex gap-4 items-center mt-5">
            <div class="bg-white rounded-lg ">
                <canvas id="chart1" width="400" height="400" ></canvas>
            </div>
            <div class="bg-white rounded-lg">
                <canvas id="chart2" width="800" height="400"></canvas>
            </div>
        </div>

    </main>
    
</body>
</html>
<script>
    const ctx = document.getElementById('chart1');
    const chart1 = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Arcemit','Generic', 'Biogesic', 'Covaxin', 'Covishield', 'Sputnik'],
            datasets: [{
                label: 'Most Ordered Products',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                    'rgba(255, 99, 132)',
                    'rgba(54, 162, 235)',
                    'rgba(255, 206, 86)',
                    'rgba(75, 192, 192)',
                    'rgba(153, 102, 255)',
                    'rgba(255, 159, 64)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],    
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx2 = document.getElementById('chart2');
    const chart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: ['Arcemit','Generic', 'Biogesic', 'Covaxin', 'Covishield', 'Sputnik'],
            datasets: [{
                label: 'Most Ordered Products',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                    'rgba(255, 99, 132)',
                    'rgba(54, 162, 235)',
                    'rgba(255, 206, 86)',
                    'rgba(75, 192, 192)',
                    'rgba(153, 102, 255)',
                    'rgba(255, 159, 64)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],    
                borderWidth: 2
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>