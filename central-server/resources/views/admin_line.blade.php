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
        var colors = ['#FFB1C1', '#FFCF9F','#4BC0C0', '#9AD0F5'];

        var dataset = []
        var date = []
        var cIndex = 0;
        @foreach ($data as $user => $userdata)
            date = [];
            var temp_data = [];
            @for ($i = 19; $i >= 0; $i--)
                temp_data.push("{{$userdata['data'][$i]}}");
            @endfor
            var temp = {
                    label: "{{$user}}",
                    backgroundColor: colors[(cIndex + 1) % 4],
                    borderColor: colors[(cIndex + 1) % 4],
                    data: temp_data,
                    fill: false,
                }
            dataset.push(temp);
            
            @for ($i = 19; $i >= 0; $i--)
                date.push("{{$userdata['dates'][$i]}}");
            @endfor  
            cIndex = cIndex +1;
        @endforeach

        // console.log(dataset)

       

		var config = {
            type: 'line',
            data: {
                labels: date,
                datasets: dataset
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
