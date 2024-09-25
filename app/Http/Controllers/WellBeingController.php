<?php

namespace App\Http\Controllers;

use App\Achievements\FamilyAndFriendsAchievement;
use App\Achievements\HealthAchievement;
use App\Achievements\HomeAchievement;
use App\Achievements\LeisureAchievement;
use App\Achievements\LoveAchievement;
use App\Achievements\MoneyAchievement;
use App\Achievements\PersonalGrowthAchievement;
use App\Achievements\WorkAchievement;
use App\FoodAssesment;
use App\HealthTest;
use App\Model\Peso;
use App\PhysicalAssessment;
use App\TrainingPreference;
use App\User;
use App\WellBeingAssessment;
use App\WheelOfLife;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class WellBeingController extends controller
{
    /**
     * Display the form to make a health test.
     */
    public function index(User $user)
    {
        return view('cliente.healthTest', compact('user'));
    }

    public function savePhysicalTest(Request $request): JsonResponse
    {
        DB::transaction(function () use ($request) {
            $physicalAssessment = new PhysicalAssessment();
            $physicalAssessment->user_id = $request->user_id;
            $physicalAssessment->muscle = $request->muscle;
            $physicalAssessment->visceral_fat = $request->visceral_fat;
            $physicalAssessment->body_fat = $request->body_fat;
            $physicalAssessment->water_level = $request->water_level;
            $physicalAssessment->proteins = $request->proteins;
            $physicalAssessment->basal_metabolism = $request->basal_metabolism;
            $physicalAssessment->bone_mass = $request->bone_mass;
            $physicalAssessment->body_score = $request->body_score;
            $physicalAssessment->body_age = $request->body_age;
            $physicalAssessment->save();

            $peso = new Peso();
            $peso->usuario_id = $request->user_id;
            $peso->peso = $request->weight;
            $peso->unidad_medida = 0;
            $peso->save();
        });

        return response()->json([
            'success' => true,
            'msg' => __('general.sucess_physical_assesment'),
            'level' => 'success',
        ], 200);
    }

    public function saveFoodTest(Request $request): JsonResponse
    {
        $foodFormAssesment = new FoodAssesment();
        $foodFormAssesment->user_id = $request->user_id;
        $foodFormAssesment->feeding_relationship = $request->feeding_relationship;
        $foodFormAssesment->breakfast = $request->breakfast;
        $foodFormAssesment->mid_morning = $request->mid_morning;
        $foodFormAssesment->lunch = $request->lunch;
        $foodFormAssesment->snacks = $request->snacks;
        $foodFormAssesment->dinner = $request->dinner;
        $foodFormAssesment->supplements = $request->supplements;
        $foodFormAssesment->medicines = $request->medicines;
        $foodFormAssesment->happy_food = $request->happy_food;
        $foodFormAssesment->sad_food = $request->sad_food;
        $foodFormAssesment->save();

        return response()->json([
            'success' => true,
            'msg' => __('general.sucess_food_assesment'),
            'level' => 'success',
        ], 200);
    }

    public function saveTrainingTest(Request $request): JsonResponse
    {
        $trainingPreference = new TrainingPreference();
        $trainingPreference->user_id = $request->user_id;
        $trainingPreference->training_frequency = $request->training_frequency;
        $trainingPreference->intensity = $request->intensity;
        $trainingPreference->music = $request->music;
        $trainingPreference->training_type = $request->training_type;
        $trainingPreference->save();

        return response()->json([
            'success' => true,
            'msg' => __('general.sucess_training_assesment'),
            'level' => 'success',
        ], 200);
    }

    public function saveWellBeingTest(Request $request): JsonResponse
    {
        $wellBeingAssessment = new WellBeingAssessment();
        $wellBeingAssessment->user_id = $request->user_id;
        $wellBeingAssessment->body_relation = $request->body_relation;
        $wellBeingAssessment->body_discomfort = $request->body_discomfort;
        $wellBeingAssessment->stress = $request->stress;
        $wellBeingAssessment->stress_practice = $request->stress_practice;
        $wellBeingAssessment->spiritual_belief = $request->spiritual_belief;
        $wellBeingAssessment->save();

        return response()->json([
            'success' => true,
            'msg' => __('general.sucess_wellbeign_assesment'),
            'level' => 'success',
        ], 200);
    }

    public function saveWheelOfLifeTest(Request $request): JsonResponse
    {
        $wheelOfLife = new WheelOfLife();
        $wheelOfLife->user_id = $request->user_id;
        $wheelOfLife->health = $request->health;
        $wheelOfLife->reason_health = $request->reason_health;
        $wheelOfLife->personal_growth = $request->personal_growth;
        $wheelOfLife->reason_personal_growth = $request->reason_personal_growth;
        $wheelOfLife->home = $request->home;
        $wheelOfLife->reason_home = $request->reason_home;
        $wheelOfLife->family_and_friends = $request->family_and_friends;
        $wheelOfLife->reason_family_and_friends = $request->reason_family_and_friends;
        $wheelOfLife->love = $request->love;
        $wheelOfLife->reason_love = $request->reason_love;
        $wheelOfLife->leisure = $request->leisure;
        $wheelOfLife->reason_leisure = $request->reason_leisure;
        $wheelOfLife->work = $request->work;
        $wheelOfLife->reason_work = $request->reason_work;
        $wheelOfLife->money = $request->money;
        $wheelOfLife->reason_money = $request->reason_money;
        $wheelOfLife->save();

        $user = User::find($request->user_id);

        if ($request->health >= 9) {
            $user->addProgress(new HealthAchievement(), 1);
        }
        if ($request->personal_growth >= 9) {
            $user->addProgress(new PersonalGrowthAchievement(), 1);
        }
        if ($request->home >= 9) {
            $user->addProgress(new HomeAchievement(), 1);
        }
        if ($request->family_and_friends >= 9) {
            $user->addProgress(new FamilyAndFriendsAchievement(), 1);
        }
        if ($request->love >= 9) {
            $user->addProgress(new LoveAchievement(), 1);
        }
        if ($request->leisure >= 9) {
            $user->addProgress(new LeisureAchievement(), 1);
        }
        if ($request->work >= 9) {
            $user->addProgress(new WorkAchievement(), 1);
        }
        if ($request->money >= 9) {
            $user->addProgress(new MoneyAchievement(), 1);
        }

        return response()->json([
            'success' => true,
            'msg' => __('general.sucess_wheel_of_life_assesment'),
            'level' => 'success',
        ], 200);
    }
}
