<?php

namespace App\View\Composers;

use App\PhysicalAssessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class PhysicalAssessmentComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $route = Route::current();
        $physicalAssessments =  PhysicalAssessment::where('user_id', $route->parameter('user')->id)
            ->orderBy('created_at')->get();
        if(!$physicalAssessments->isEmpty()) {
            $physicalAssessmentDates = $physicalAssessments->pluck('created_at')->map(function ($date) {
                return Carbon::parse($date)->format('Y-m-d');
            });
            //$body_score = $physicalAssessments->last()->body_score;
            $body_age = $physicalAssessments->last()->body_age;

            $datasets = [
                [
                    'label' => 'Muscle',
                    'data' => $physicalAssessments->pluck('muscle'),
                    'backgroundColor' => 'rgba(75, 192, 192, 1)',
                    'borderColor' => 'rgba(75, 192, 192, 1)'
                ],
                [
                    'label' => 'Visceral Fat',
                    'data' => $physicalAssessments->pluck('visceral_fat'),
                    'backgroundColor' => 'rgba(255, 99, 132, 1)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                ],
                [
                    'label' => 'Body Fat',
                    'data' => $physicalAssessments->pluck('body_fat'),
                    'backgroundColor' => 'rgba(54, 162, 235, 1)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                /*
                [
                    'label' => 'Water Level',
                    'data' => $physicalAssessments->pluck('water_level'),
                    'backgroundColor' => 'rgba(255, 206, 86, 1)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                ],
                [
                    'label' => 'Proteins',
                    'data' => $physicalAssessments->pluck('proteins'),
                    'backgroundColor' => 'rgba(153, 102, 255, 1)',
                    'borderColor' => 'rgba(153, 102, 255, 1)',
                ],
                [
                    'label' => 'Bone Mass',
                    'data' => $physicalAssessments->pluck('bone_mass'),
                    'backgroundColor' => 'rgba(255, 99, 132, 1)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                ],
                */
            ];

            $view->with([
                'physicalAssessments' => $physicalAssessments,
                'physicalAssessmentDates' => $physicalAssessmentDates->toJson(),
                'datasets' => $datasets,
                'body_age' => $body_age,
            ]);
        }
    }
}