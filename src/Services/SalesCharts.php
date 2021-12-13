<?php

namespace Services\SalesCharts;

use Carbon\Carbon;

class SalesCharts
{
    private $labels, $data, $options = [], $label_data, $chart;



    /**
     * @return array
     */
    public function getOptions(): array
    {


        return $this->options;
    }


    public function __construct(...$options)
    {
        $opts = [];
        foreach ($options as $key => $opt) {



            $opts[] = $opt;

            $opts[$key]['chart_options']['label'] = $opts[$key]['label_name'];
            if(!isset($opt['chart_options']['backgroundColor'])){

                $opts[$key]['chart_options']['backgroundColor']= 'rgba(210, 214, 222, 1)';


            }

            if(!isset($opt['chart_options']['borderColor'])){
                $opts[$key]['chart_options']['borderColor']=  'rgba(210, 214, 222, 1)';

            }


            $opts[$key]['chart_options']['pointRadius'] = 'false';
            $opts[$key]['chart_options']['pointColor'] = 'rgba(210, 214, 222, 1)';
            $opts[$key]['chart_options']['pointStrokeColor'] = '#c1c7d1';
            $opts[$key]['chart_options']['pointHighlightFill'] = '#fff';
            $opts[$key]['chart_options']['pointHighlightStroke'] = 'rgba(220,220,220,1)';

        }



        $this->setOptions($opts);

    }


    /**
     * @return mixed
     */
    public function getChart($key)
    {
        return $this->chart[$key];
    }

    /**
     * @return mixed
     */
    public function getLabelData()
    {
        return $this->label_data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array $options
     * @return SalesCharts
     */
    public function setOptions(array $options): SalesCharts
    {

        $this->options = $options;

        return $this;
    }


    /**
     * @param mixed $labels
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
        return $this;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }


    public function ChartRender()
    {
        $field = $this->options[0]['label_field'] ?? 'created_at';


        foreach ($this->prepareDataForLabel() as $label) {


            $date[] = "'" . Carbon::create($label->$field)->format("M Y") . "'";


        }


        $date = array_unique($date);


        foreach ($this->prepareDataForChartData() as $key => $completed_project) {

            foreach ($completed_project as $keys => $cp) {


                $completed_project_array[$key][$keys] = $cp->sum('price');
            }
            $this->chart[$key] = implode(", ", $completed_project_array[$key]);
        }


        $this->label_data = implode(", ", $date);


        return $this;


    }

    private function prepareDataForLabel()
    {

        if (!class_exists($model = $this->options[0]['model'])) {

            throw new \Exception("Model $model Does Not Exists");
        }


        return $this->options[0]['model']::when(isset($this->options[0]['order_by']), function ($func) {

            return $func->orderBy($this->options[0]['order_by']);

        })->when(isset($this->options[0]['conditions']), function ($func) {

            return $func->where($this->options[0]['conditions']);
        })->get();


    }


    private function prepareDataForChartData()
    {
        $data = [];
        foreach ($this->options as $opt) {

            $model = $opt['model']::query();
            $cond = $opt['conditions'] ?? null;


            if (isset($opt['join_condition']) && isset($opt['join_type'])) {

                $join_type = $opt['join_type'];
                $condition = $opt['join_condition'];

                if (!isset($condition[2])) {

                    throw new \Exception("Join condition requires 3 parameters");

                }
                $model->{$join_type}($condition[0], $condition[1], $condition[2]);

            }

            if (isset($opt['select_condition'])) {
                if (!is_callable($opt['select_condition'])) {

                    throw new \Exception("Options Select Condition Must Be callable");
                }

                $select_condition = $opt['select_condition'];


                $model->when(isset($select_condition), $select_condition);

            }

            $datas = $model->when(isset($opt['order_by']), function ($func) use ($opt) {

                return $func->orderBy($opt['order_by']);

            })->when(isset($cond), function ($func) use ($cond) {

                return $func->where($cond);
            })->get();

            if (isset($opt['map_condition'])) {

                $datas = $datas->map($opt['map_condition']);

            }

            if (isset($opt['group_by'])) {
                $datas = $datas->groupBy($opt['group_by']);

            }


            $data[$opt['label_name']] = $datas;
        }

        return $data;
    }


    public function renderHtml()
    {

        $options = $this->options;
        $chart_title = $this->options[0]['chart_title'] ?? 'chart_load';

        return view('charts_view::chart_html', compact('options', 'chart_title'));

    }

    public function renderScripts()
    {


        echo '<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>';

        $chart_title = $this->options[0]['chart_title'] ?? 'chart_load';
        $options = $this->getOptions();
        $charts = $this;

        return view('charts_view::charts_scripts', compact('chart_title', 'options', 'charts'));

    }


}
