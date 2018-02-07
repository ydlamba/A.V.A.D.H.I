@extends('layouts.dashboard_template')

@section('heading')
	Performance
@endsection


@section('content')

	<div class="chart-wrapper">
		<div class="chart-container">
			<canvas id="pie-chart"></canvas>
		</div>
		<div class="chart-text">
			Time Spent In the Week: {{ $total }} Hours 
		</div>
	</div>
	<script>
		
		var data = [ {{$data[6]}},{{$data[5]}},{{$data[4]}},{{$data[3]}},{{$data[2]}},{{$data[1]}},{{$data[0]}}];	
		var date = [ "{{$date[6]}}","{{$date[5]}}","{{$date[4]}}","{{$date[3]}}","{{$date[2]}}","{{$date[1]}}","{{$date[0]}}"];
		var ctx = document.getElementById("pie-chart");
		var myChart = new Chart(ctx, {
						    type: 'pie',
					        data: {
					            datasets: [{
					                data: data,
					                backgroundColor: [
					                    '#FF6384',
					                    '#FF9F40',
					                    '#FFCD56',
					                    '#36A2EB',
					                    '#4BC0C0',
					                    '#FF6384',
					                    '#FF9F40'
					                ],
					                label: 'Hours'
					            }],
					            labels: date
					        },
					        options: {
					            responsive: true
					        }
						});

	</script>
@endsection
