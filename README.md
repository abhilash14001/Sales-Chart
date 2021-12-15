# Sales-Chart
Generate Charts for Sales Purposes
## Installation

```
composer require abhilash/sales-chart
```
## Usage

![Sales Charts - Projects which is ongoing and completed](https://vymaps.team24seven.online/assets/images/chart.png)

If you want to generate a chart above, grouping __project_price__ by month and year of __project_submit_date__ or __created_at__ value according to your choice, here's the code.

__Controller__:

```
use Abhilash\SalesCharts\SalesChartServices\SalesCharts;

// ...

  $groupBy = function ($item) {

            return Carbon::create($item->project_submit_date)->format("Y-m");


        };

        $map_condition_for_completed = function ($func) {


            if ($func->status != 'completed') {

                $func->price = 0;
            }

            return $func;

        };



        $map_condition_for_ongoing = function ($func) {


            if ($func->status != 'ongoing') {

                $func->price = 0;
            }

            return $func;

        };

        $select_condition = function ($query) {
            return $query->select('payment_projects.price as price', 'projects.price as total_price', 'project_submit_date', 'status');
        };
        $options_for_completed_project = [
            'chart_type' => 'line',
            'label_name' => 'Completed',
            'chart_title' => 'sales_price',
            'model' => Projects::class,                //model name to be entered
            'order_by' => 'project_submit_date',
            'label_field' => 'project_submit_date',
            'group_by' => $groupBy,
            'map_condition' => $map_condition_for_completed,
            'chart_options' => [
                'backgroundColor' => 'rgba(60, 141, 188, 0.9)',
                'borderColor' => 'rgba(60, 141, 188, 0.8)',
            ]
        ];



        $options_for_ongoing_project = [
            'chart_type' => 'line',
            'label_name' => 'Ongoing',
            'chart_title' => 'sales_price',
            'model' => Projects::class,                                 //model name to be entered

  'order_by' => 'project_submit_date',
            'label_field' => 'project_submit_date',
            'group_by' => $groupBy,
            'map_condition' => $map_condition_for_ongoing,
            'join_type' => 'leftjoin',
            'select_condition' => $select_condition,

            'join_condition' => ['payment_projects', 'payment_projects.projects_id', 'projects.id'],

            'chart_options' => [

                'backgroundColor' => 'rgba(210, 214, 222, 1)',
                'borderColor' => 'rgba(210, 214, 222, 1)'
            ],
        ];
$chart = new SalesCharts($options_for_ongoing_project, $options_for_completed_project);     //can pass either 1 or 2 arguments based on your requirement
 
return view('dashboard', compact('chart'));
```


__View File__

```
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">

                    
                    {!! $chart->renderHtml() !!}

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
{!! $chart1->renderScripts() !!}
@endsection
