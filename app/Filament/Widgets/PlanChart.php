<?php

namespace App\Livewire;

use App\Models\financialPlanModel;
use App\Models\financialPlanProgressModel;
use Filament\Widgets\ChartWidget;

class PlanChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $dataPlan = financialPlanModel::where('user_id',auth()->id());
        foreach($dataPlan as $dp){
            // return $dataProgressPlan = financialPlanProgressModel::where('id_financial', $dp->id);
        }
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
