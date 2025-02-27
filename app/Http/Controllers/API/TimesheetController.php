<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timesheet;
use Illuminate\Support\Facades\Validator;

class TimesheetController extends Controller
{
    // Read: Get all timesheets
    public function index()
    {
        $timesheets = Timesheet::all();

        return response()->json([
            'status' => 200,
            'data' => $timesheets
        ]);
    }

    // Create: Store a new timesheet
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_name' => 'required|string',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0|max:24',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => "Validation error",
                "data"    => $validator->errors()->all()
            ]);
        }

        $timesheet = Timesheet::create([
            'task_name' => $request->task_name,
            'date'      => $request->date,
            'hours'     => $request->hours,
            'user_id' => $request->user_id,
            'project_id' => $request->project_id,   
        ]);

        return response()->json([
            'status'  => 201,
            'message' => 'Timesheet created successfully',
            'data'    => $timesheet
        ], 201);
    }

    // Read: Get a single timesheet
    public function show($id)
    {
        $timesheet = Timesheet::find($id);

        if (!$timesheet) {
            return response()->json([
                'status' => 404,
                'message' => 'Timesheet not found',
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'data' => $timesheet
        ]);
    }

    // Update: Update an existing timesheet
    public function update(Request $request, $id)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0|max:24',
        ]);

        $timesheet = Timesheet::find($id);

        if (!$timesheet) {
            return response()->json([
                'status' => 404,
                'message' => 'Timesheet not found',
            ], 404);
        }

        $timesheet->update([
            'task_name' => $request->task_name,
            'date' => $request->date,
            'hours' => $request->hours,
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Timesheet updated successfully',
            'data' => $timesheet
        ]);
    }

     // Delete: Delete a timesheet
     public function destroy($id)
     {
         $timesheet = Timesheet::find($id);
 
         if (!$timesheet) {
             return response()->json([
                 'status' => 404,
                 'message' => 'Timesheet not found',
             ], 404);
         }
 
         $timesheet->delete();
 
         return response()->json([
             'status' => 200,
             'message' => 'Timesheet deleted successfully'
         ]);
     }
}
