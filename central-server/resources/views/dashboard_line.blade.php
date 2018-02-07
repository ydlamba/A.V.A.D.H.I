@extends('layouts.dashboard_template')

@section('heading')
	Performance
@endsection


@section('content')

	<div class="chart-wrapper">
		<div class="chart-container">
			<canvas id="line-chart"></canvas>
		</div>
		<div class="chart-text">
			Time Spent In the Week: {{ $total }} Hours 
		</div>
	</div>
	<script>
		
		var data = [ {{$data[6]}},{{$data[5]}},{{$data[4]}},{{$data[3]}},{{$data[2]}},{{$data[1]}},{{$data[0]}}];	
		var date = [ "{{$date[6]}}","{{$date[5]}}","{{$date[4]}}","{{$date[3]}}","{{$date[2]}}","{{$date[1]}}","{{$date[0]}}"];
		var ctx = document.getElementById("line-chart");
		var myChart = new Chart(ctx, {
						    type: 'line',
						    data: {
						        labels: date,
						        datasets: [{
						            label: 'Hours Active',
						            data: data,
						            backgroundColor: [
						                'rgba(255, 99, 132, 0.2)',
						                'rgba(54, 162, 235, 0.2)',
						                'rgba(255, 206, 86, 0.2)',
						                'rgba(75, 192, 192, 0.2)',
						                'rgba(153, 102, 255, 0.2)',
						                'rgba(255, 159, 64, 0.2)'
						            ],
						            borderColor: [
						                'rgba(255,99,132,1)',
						                'rgba(54, 162, 235, 1)',
						                'rgba(255, 206, 86, 1)',
						                'rgba(75, 192, 192, 1)',
						                'rgba(153, 102, 255, 1)',
						                'rgba(255, 159, 64, 1)'
						            ],
						            borderWidth: 1
						        }]
						    },
						    options: {
						    	responsive:true,
				                scales: {
				                	ticks: {
				                		beginAtZero: true
				                	},
				                    yAxes: [{
				                        display: true,
				                        scaleLabel: {
				                            display: true,
				                            labelString: 'Hours'
				                        }
				                    }]
				                }
						    }
						});

	</script>
@endsection
