@extends('layouts.dashboard_admin')

@section('heading')
	Time Scores
@endsection


@section('content')

<div class="chart-wrapper">
		<div class="chart-container">
			<canvas id="line-chart"></canvas>
		</div>
		<div class="chart-text">
			Time Scores Over the Week
		</div>
	</div>
	<script>
		var ctx = document.getElementById("line-chart");

		var config = {
            type: 'line',
            data: {
                labels: ["January", "February", "March", "April", "May", "June", "July"],
                datasets: [{
                    label: "kataria",
                    backgroundColor: '#FFB1C1',
                    borderColor: '#FFB1C1',
                    data: [
                        4,3,1,2,3,4,1
                    ],
                    fill: false,
                }, {
                    label: "kataria1",
                    fill: false,
                    backgroundColor: '#4BC0C0',
                    borderColor: '#4BC0C0',
                    data: [
                        3,2,5,2,4,1,6
                    ],
                }]
            },
            options: {
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Day'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Hours'
                        }
                    }]
                }
            }
        };
		var chart = new Chart(ctx,config);

	</script>
	
@endsection
