<?php

namespace App\Http\Controllers\API;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\AttributeValue;
use App\Models\User;
use App\Models\Attribute;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projects = Project::with('attributeValues.attribute')->get();
        return response()->json([
            "status"  => 200,
            "message" => "Projects retrieved successfully",
            "data"    => $projects
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'status'             => 'required|in:pending,in_progress,completed',
            'attributes'         => 'sometimes|array',
            'attributes.*.id'    => 'exists:attributes,id',
            'attributes.*.value' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => "Validation error",
                "data"    => $validator->errors()->all()
            ]);
        }

        $project = Project::create([
            'name'   => $request->name,
            'status' => $request->status,
        ]);

        // Store dynamic attributes
        if ($request->has('attributes')) {
            foreach ($request->input('attributes') as $attr) {
                AttributeValue::create([
                    'attribute_id' => $attr['id'],
                    'entity_id'    => $project->id,
                    'value'        => $attr['value'],
                ]);
            }
        }

        return response()->json([
            "status"  => 201,
            "message" => "Project created successfully",
            "data"    => $project->load('attributeValues.attribute')
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id,Request $request)
    {
        $project = Project::with('attributeValues.attribute')->find($id);
        if (!$project) {
            return response()->json([
                "status"  => 404,
                "message" => "Project not found"
            ]);
        }

        return response()->json([
            "status"  => 200,
            "message" => "Project retrieved successfully",
            "data"    => $project
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                "status"  => 404,
                "message" => "Project not found"
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name'   => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status"  => 400,
                "message" => "Validation error",
                "data"    => $validator->errors()->all()
            ]);
        }

        $project->update($request->only(['name', 'status']));

        return response()->json([
            "status"  => 200,
            "message" => "Project updated successfully",
            "data"    => $project
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                "status"  => 404,
                "message" => "Project not found"
            ]);
        }

        $project->delete();

        return response()->json([
            "status"  => 200,
            "message" => "Project deleted successfully"
        ]);
    }

    /**
     * Validate incoming attributes 
     * (assuming 'attributes' is an array of attributes)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAttributes(Request $request, $projectId)
    {
        $request->validate([
            'attributes'         => 'required|array',
            'attributes.*.id'    => 'required|exists:attributes,id',   // Validate if the attribute ID exists
            'attributes.*.value' => 'required|string',                 // Assuming value is a string, you can modify this based on the attribute type
        ]);

        // Find the project (make sure it exists)
        $project = Project::with('attributeValues.attribute')->find($projectId);

        if (!$project) {
            return response()->json([
                'status' => 404,
                'message' => 'Project not found!',
            ], 404);
        }

        // Loop through each attribute to update or create AttributeValue records
        foreach ($request->input('attributes') as $attr) {
            // Check if the attribute value exists for the given project (entity_id)
            $existingAttributeValue = AttributeValue::where('attribute_id', $attr['id'])
                                                    ->where('entity_id', $project->id)
                                                    ->first();

            if ($existingAttributeValue) {
                // If it exists, update the existing record
                $existingAttributeValue->update([
                    'value' => $attr['value'],
                ]);
            } else {
                // If it doesn't exist, create a new record
                AttributeValue::create([
                    'attribute_id' => $attr['id'],
                    'entity_id'    => $project->id,
                    'value'        => $attr['value'],
                ]);
            }
        }

        return response()->json([
            "status"  => 200,
            "message" => "Project Attributes updated successfully",
            "data"    => $project
        ]);

    }

    /**
     * Attach a user to a project
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function assignUserToProject($projectId, $userId)
    {
        $project = Project::find($projectId);
        $user = User::find($userId);

        if (!$project || !$user) {
            return response()->json(['message' => 'Project or User not found'], 404);
        }

        $project->users()->attach($user);

        return response()->json(['message' => 'User assigned to project successfully'], 200);
    }

    /**
     * Fetch projects with dynamic attributes and support flexible filtering
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProjectsWithAttributes(Request $request)
    {
        $query = Project::query();
        
        // Filter by regular attributes (like name, status)
        if ($request->has('filters')) {
            $filters = $request->input('filters');
            
            foreach ($filters as $key => $value) {
                // Check if the filter is a regular project attribute (name, status, etc.)
                if (in_array($key, ['name', 'status'])) {
                    $this->applyRegularFilter($query, $key, $value);
                }
                // Check if the filter is for an EAV attribute (e.g., department, start_date)
                else {
                    $this->applyEAVFilter($query, $key, $value);
                }
            }
            // dd($query->toSql(), $query->getBindings());

        }

        // Eager load the attributes and their values for the projects
        $projects = $query->with(['attributes', 'attributeValues'])->get();

        return response()->json([
            'status' => 200,
            'data' => $projects
        ]);
    }

    // Apply filtering on regular project attributes (name, status, etc.)
    private function applyRegularFilter($query, $key, $value)
    {
        // Operators support for regular fields
        if (strpos($value, '>=') === 0) {
            $query->where($key, '>=', substr($value, 2));
        } elseif (strpos($value, '<=') === 0) {
            $query->where($key, '<=', substr($value, 2));
        } elseif (strpos($value, '>') === 0) {
            $query->where($key, '>', substr($value, 1));
        } elseif (strpos($value, '<') === 0) {
            $query->where($key, '<', substr($value, 1));
        } elseif (strpos($value, '%') === false) {
            $query->where($key, '=', $value);
        } else {
            $query->where($key, 'LIKE', $value);
        }
    }

    // Apply filtering on EAV attributes (dynamic fields like department, start_date)
    private function applyEAVFilter($query, $attributeName, $value)
    {
        // Handle operators for EAV values
        $operator = '=';
        if (strpos($value, '>=') === 0) {
            $operator = '>=';
            $value = substr($value, 2);
        } elseif (strpos($value, '<=') === 0) {
            $operator = '<=';
            $value = substr($value, 2);
        } elseif (strpos($value, '>') === 0) {
            $operator = '>';
            $value = substr($value, 1);
        } elseif (strpos($value, '<') === 0) {
            $operator = '<';
            $value = substr($value, 1);
        } elseif (strpos($value, '%') !== false) {
            $operator = 'LIKE';
        }

        // Get the attribute ID for the dynamic attribute (like department, start_date)
        $attribute = Attribute::where('name', $attributeName)->first();

        if ($attribute) {
            // Filter projects based on the attribute value in the AttributeValue table
            $query->whereHas('attributeValues', function ($subQuery) use ($attribute, $value, $operator) {
                $subQuery->where('attribute_id', $attribute->id)
                         ->where('value', $operator, $value);
            });
        }
    }
    
}
