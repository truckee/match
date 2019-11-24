<?php

/*
 * (c) GWB truckeesolutions@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//src/Services/ChartService.php

namespace App\Services;

//use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ColumnChart;

/**
 * 
 */
class ChartService
{

    public function volunteerChart()
    {
        $m = date_format(new \DateTime(), 'm');
        for ($i = 0; $i < 12; $i++) {
            $pm = ($m + $i <= 12) ? $m + $i : $m + $i - 12;
            $month[] = ($m + $i <= 12) ? date("M", mktime(0, 0, 0, $m + $i, 10)) : date("M", mktime(0, 0, 0, $m + $i - 12, 10));
        }

        $chart = new \CMEN\GoogleChartsBundle\GoogleCharts\Charts\Material\ColumnChart();
        $chart->setElementID('div-chart')
                ->getData()->setArrayToDataTable([
            ['Month', '#'],
            [$month[0], 4],
            [$month[1], 9],
            [$month[2], 16],
            [$month[3], 23],
            [$month[4], 29],
            [$month[5], 34],
            [$month[6], 38],
            [$month[7], 41],
            [$month[8], 44],
            [$month[9], 46],
            [$month[10], 47],
            [$month[11], 48]
        ]);
        $chart->getOptions()->getChart()
                ->setTitle('Volunteer Registrations')
                ->setSubtitle('Previous 12 months');
        $chart->getOptions()
                ->getLegend()->setPosition('none');
        $chart->getOptions()
                ->setBars('vertical')
                ->setHeight(300)
                ->setWidth(450);

        return $chart;
    }

}
