<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $projects = Project::orderBy('title')->paginate(15);
        return new JsonResponse($projects, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     * @param Project $project
     * @return JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        return new JsonResponse($project, Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     * @param ProjectRequest $request
     * @return JsonResponse
     */
    public function store(ProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());
        return new JsonResponse(['data' => $project, 'msg' => 'Project created successfully'], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProjectRequest  $request
     * @param  Project  $project
     * @return JsonResponse
     */
    public function update(ProjectRequest $request, Project $project): JsonResponse
    {
        $project->update($request->validated());
        return new JsonResponse(['data' => $project, 'msg' => 'Project updated successfully'], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Project  $project
     * @return JsonResponse
     */
    public function destroy(Project $project): JsonResponse
    {
        if($project->delete()){
            return new JsonResponse(['data' => [], 'msg' => 'Project deleted successfully'], Response::HTTP_OK);
        }

        return new JsonResponse(['msg' => 'Something went wrong, please try again'], Response::HTTP_BAD_REQUEST);
    }
}
