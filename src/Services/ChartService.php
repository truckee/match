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
                ->setHeight(220)
                ->setWidth(330);

        return $chart;
    }

    public function searchGauge()
    {
        $gauge = new \CMEN\GoogleChartsBundle\GoogleCharts\Charts\GaugeChart;
        $gauge->setElementID('div-search')->getData()->setArrayToDataTable([
            ['Label', 'Value'],
            ['Search', 40],
        ]);
        $gauge->getOptions()->setWidth(400);
        $gauge->getOptions()->setHeight(120);
        $gauge->getOptions()->setRedFrom(0);
        $gauge->getOptions()->setRedTo(10);
        $gauge->getOptions()->setYellowFrom(10);
        $gauge->getOptions()->setYellowTo(20);
        $gauge->getOptions()->setGreenFrom(20);
        $gauge->getOptions()->setGreenTo(100);
        $gauge->getOptions()->setMinorTicks(5);

        return $gauge;
    }

    public function sankeyFocus()
    {
        $focus = new \CMEN\GoogleChartsBundle\GoogleCharts\Charts\SankeyDiagram();
        $focus->setElementID('div_focus')->getData()->setArrayToDataTable(
            [
                    [['label' => 'Focus', 'type' => 'string'], ['label' => 'Nonprofit', 'type' => 'string'],
                        ['label' => 'Weight', 'type' => 'number']],
                    ['Education', 'Alpha', 5],
                    ['Education', 'Beta', 7],
                    ['Education', 'Gamma', 6],
                    ['Health', 'Alpha', 5],
                    ['Health', 'Beta', 9],
                    ['Health', 'Gamma', 4],
                    ['Seniors', 'Alpha', 9],
                    ['Seniors', 'Beta', 5],
                    ['Seniors', 'Gamma', 4]
        ]
        );
        $focus->getOptions()->setWidth(300);
        $focus->getOptions()->setHeight(200);
        $focus->getOptions()->getSankey()->getNode()->setColors(['#a6cee3', '#b2df8a', '#fb9a99', '#fdbf6f', '#cab2d6', '#ffff99', '#1f78b4', '#33a02c']);
        $focus->getOptions()->getSankey()->getLink()->setColors(['#a6cee3', '#b2df8a', '#fb9a99', '#fdbf6f', '#cab2d6', '#ffff99', '#1f78b4', '#33a02c']);
        $focus->getOptions()->getSankey()->getLink()->setColorMode('gradient');
        $focus->getOptions()->getSankey()->getNode()->getLabel()->setFontName('Times-Roman');
        $focus->getOptions()->getSankey()->getNode()->getLabel()->setFontSize(14);
        $focus->getOptions()->getSankey()->getNode()->getLabel()->setColor('#871b47');
        $focus->getOptions()->getSankey()->getNode()->getLabel()->setItalic(true);
        $focus->getOptions()->getSankey()->getNode()->getLabel()->setBold(true);
        
        return $focus;
    }
}
