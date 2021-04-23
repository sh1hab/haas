<?php


namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CalendarController extends Controller
{
    /**
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function showCalendar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable:date',
            'sign' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $year = $request->input('year', date('Y'));
        $sign = $request->input('sign', 'aries');

        $data = Calendar::where('year', '=', $year)->first();

        $signs = (config('constants.signs.names'));

        if (is_null($data)) {
            for ($index = 1; $index <= 12; $index++) {
                $res = $this->calculateScore($year);
                Calendar::insert([
                    'year' => $year,
                    'sign' => $signs[$index - 1],
                    'yearly_score' => $res['yearly_score'],
                    'best_month' => $res['best_month'],
                    'months_score_details' => $res['months_score_details']
                ]);
            }
        }

        $calendar_data = Calendar::where('year', $year)
            ->where('sign', $sign)
            ->first();

        $highest_scored_sign = Calendar::where('year', $year)
            ->orderBy('yearly_score', 'desc')
            ->first()['sign'];

        return view('calendar/index', compact('highest_scored_sign', 'calendar_data', 'year','sign'));
    }

    private function calculateScore(int $year): array
    {
        $max_score = 10;
        $days_in_a_year = (date('L', strtotime("$year-01-01")) ? 366 : 365);
        $calendar = range(1, $days_in_a_year);
        $calendar = array_map(function ($value) use ($max_score) {
            return (($value + mt_rand(1, 10)) % $max_score) + 1;
        }, $calendar);
        shuffle($calendar);
        $yearly_score = array_sum($calendar);

        $res = $this->calculateMonthlyScore($calendar, $year);
        unset($calendar);

        return [
            'yearly_score' => $yearly_score,
            'best_month' => $res['best_month'],
            'months_score_details' => json_encode($res['months_score_details'])
        ];
    }

    private function calculateMonthlyScore(array $calendar, int $year): array
    {
        $months = [];
        $best_month = '';
        $best_month_score = 0;
        for ($month = 1; $month <= 12; $month++) {
            $days_in_a_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $months[$month] = array_slice($calendar, 0, $days_in_a_month);
            $calendar = array_slice($calendar, $days_in_a_month);
            if (array_sum($months[$month]) > $best_month_score) {
                $best_month_score = array_sum($months[$month]);
                $best_month = $month;
            }
        }

        return [
            'best_month' => $best_month,
            'months_score_details' => $months
        ];
    }
}
