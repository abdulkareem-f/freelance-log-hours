<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimeLogRequest;
use App\Models\TimeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TimeLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $timeLogs = TimeLog::where('user_id', $request->user()->id)
                    ->orderBy('start_time', 'desc')->with('project')
                    ->paginate(15);

        return new JsonResponse($timeLogs, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     * @param TimeLogRequest $request
     * @param int|null $id
     * @return JsonResponse
     */
    public function log(TimeLogRequest $request, int $id = null): JsonResponse
    {
        $user = $request->user();
        $data = array_merge($request->validated(), ['user_id' => $user->id]);
        $data = $this->refactorData($data);

        if ($id) {
            $timeLog = TimeLog::where('id', $id)->firstOrFail();
            $timeLog->update($data);
        } else {
            $lastTimeLog = $user->timeLogs->last();
            if(!$lastTimeLog->end_time){
                return new JsonResponse(['msg' => 'You can\'t start or log new time log, stop the current log first' ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $timeLog = TimeLog::create($data);
        }
        return new JsonResponse(['data' => $timeLog, 'msg' => 'Time has been logged successfully'], Response::HTTP_OK);
    }

    private function refactorData($data): array
    {
        $status = $data['status'] ?? 'start';
        switch ($status) {
            case 'start':
                $data['start_time'] = Carbon::now()->format('Y-m-d H:i:s');
                break;

            case 'end':
                $data['end_time'] = Carbon::now()->format('Y-m-d H:i:s');
                break;
        }
        unset($data['status']);

        return $data;
    }

    /**
     * Display a listing of the resource.
     * @param int $id
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse
    {
        $timeLog = TimeLog::findOrFail($id)->load('project', 'user');
        return new JsonResponse($timeLog, Response::HTTP_OK);
    }

    public function evaluation(Request $request): JsonResponse
    {
        $user = $request->user();

        $dailyLogs = TimeLog::selectRaw('DATE(start_time) as day, SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours')
            ->where('user_id', $user->id)
            ->groupBy('day')
            ->get();

        $weeklyLogs = TimeLog::selectRaw('YEAR(start_time) as year, WEEK(start_time, 1) as week, SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours')
            ->where('user_id', $user->id)
            ->groupBy('year', 'week')
            ->get();

        $monthlyLogs = TimeLog::selectRaw('YEAR(start_time) as year, MONTH(start_time) as month, SUM(TIMESTAMPDIFF(HOUR, start_time, end_time)) as total_hours')
            ->where('user_id', $user->id)
            ->groupBy('year', 'month')
            ->get();


        return new JsonResponse([
            'daily' => $dailyLogs,
            'weekly' => $weeklyLogs,
            'monthly' => $monthlyLogs
        ], Response::HTTP_OK);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $data = TimeLog::where('user_id', $user->id)->with('project')->get();
        $filename = 'time_logs.csv';

        $csv = $this->generateCSV($data);

        $filePath = public_path($filename);

        File::put($filePath, $csv);

        return response()->json(['file_path' => asset($filename)], 200);
    }

    private function generateCSV($data)
    {
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, ['Freelance Name', 'Project', 'Started', 'Finished', 'hours', 'notes']); // Add column headers

        foreach ($data as $row) {
            fputcsv($handle, [$row->user->name, $row->project?->title, $row->start_time, $row->end_time, $row->hours, $row->notes]); // Replace with your data columns
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return $csv;
    }

    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        $lastTimeLog = $user->timeLogs->last();
        $data['status'] = 'stopped';
        $data['last_log'] = $lastTimeLog;

        if(!$lastTimeLog->end_time){
            $data['status'] = 'running';
        }

        return new JsonResponse($data, Response::HTTP_OK);
    }

    public function destroy(TimeLog $timeLog): JsonResponse
    {
        if($timeLog->delete()){
            return new JsonResponse(['data' => [], 'msg' => 'Time Log deleted successfully'], Response::HTTP_OK);
        }

        return new JsonResponse(['msg' => 'Something went wrong, please try again'], Response::HTTP_BAD_REQUEST);
    }
}
