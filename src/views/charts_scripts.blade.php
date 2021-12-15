
<script>
/* Chart.js Charts */
// Sales chart
var salesChartCanvas = document.getElementById('{{$chart_title}}').getContext('2d')

// $('#revenue-chart').get(0).getContext('2d');


var salesChartData = {

    labels: [{!!  $charts->getLabelData() !!}],
    datasets: [
            @foreach($charts->getOptions() as $key1 => $chartOptions)
        {
            @foreach($chartOptions['chart_options'] as $key => $charto)

                @if($key != 'pointRadius')
                {{$key}} : '{{$charto}}',


            @else
                {{$key}} : {{$charto}},

            @endif

                @endforeach
            data: [{{$charts->getChart($chartOptions['label_name'])}}]

        },

        @endforeach

    ]
}
var salesChartOptions = {
    maintainAspectRatio: false,
    responsive: true,
    legend: {
        display: false
    },
    scales: {
        xAxes: [{
            gridLines: {
                display: true
            }
        }],
        yAxes: [{
            gridLines: {
                display: true
            }
        }]
    }
}

new Chart(salesChartCanvas, {
    type: '{{$charts->getOptions()[0]['chart_type']}}',
    data: salesChartData,
    options: salesChartOptions
})
</script>

