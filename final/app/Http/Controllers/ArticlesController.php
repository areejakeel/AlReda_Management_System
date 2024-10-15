<?php

namespace App\Http\Controllers;
use App\Models\Articles;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{
    public function index()
    {
        //
    }

   
public function store_Article(Request $request,$id)
{     

  $input = $request->all();
  $validator = Validator::make($input, [
      'title' => 'required',
      'content' => 'required'
  ]);

  if ($validator->fails()) {
      return response()->json($validator->errors()->tojson(), 400);
  }
  $doctor =Doctor::find($id);

  $article = new Articles();

  $article->fill([
    'title' => $input['title'],
    'content' => $input['content'],
    'doctors_id' => $id,
]);
  // Set the initial id

  $article->save();

  return response()->json([
      'data' => $article,
      'doctor'=>$doctor,
      'message' => 'doctor  added his articles successfully'
  ]);
}

    public function show($doctors_id)
    {
    $doctor=Doctor::find($doctors_id);
   $articles=Articles::returnarticlesfordoctor($doctors_id);
  
     return response()->json([
        'doctor'=>$doctor->first_name.' '.$doctor->last_name,
        'articles'=>$articles
     ],200);
    }
  
    public function show2($doctors_id)
    {
    $doctor=Doctor::find($doctors_id);
   $articles=Articles::returnarticlesfordoctor($doctors_id);
  
     return response()->json([
        'doctor'=>$doctor->first_name.' '.$doctor->last_name,
        'articles'=>$articles
     ],200);
    }  public function getAllArticle()
{
    $articles = Articles::join('doctors', 'articles.doctors_id', '=', 'doctors.id')
                        ->select('articles.*', DB::raw("CONCAT(doctors.first_name, ' ', doctors.last_name) as doctor_name"))
                        ->get();

    return response()->json(['data' => $articles]);
}

   
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
