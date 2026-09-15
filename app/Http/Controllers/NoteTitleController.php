<?php

namespace App\Http\Controllers;
use App\Models\NoteTitleModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class NoteTitleController extends Controller
{
    //
     public function index()
    {
         $data = [
             'page_title' => 'Note Title',
            'page_name'  => 'Note Title',
        ];
        return view('company.master.note_title', $data);
    }
     public function getList()
    {
        try {

            $permissions = checkPermissions(get_index_route($this));

            $canUpdate = $permissions['canUpdate'] ?? false;
            $canDelete = $permissions['canDelete'] ?? false;

            $query = NoteTitleModel::select([
                    'note_title_id',
                    'note_title',
                    'created_by',
                    'created_at'
                ])
                ->where('status',0);
               

            return DataTables::of($query)

                ->editColumn('created_by', function ($row) {
                    return get_createdby_name($row->created_by);
                })

                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)
                        ->format('d-m-Y H:i:s');
                })

                ->addColumn('action', function ($row) use ($canUpdate,$canDelete){

                    $buttons = '<div class="d-flex gap-1 justify-content-center">';

                    if($canUpdate){
                        $buttons .= '
                        <button
                            class="btn btn-sm btn-primary edit-group"
                            data-id="'.$row->note_title_id.'">
                            <i class="fas fa-edit"></i> Edit
                        </button>';
                    }

                    if($canDelete){
                        $buttons .= '
                        <button
                            class="btn btn-sm btn-danger delete-group"
                            data-id="'.$row->note_title_id.'">
                            <i class="fas fa-trash"></i> Delete
                        </button>';
                    }

                    $buttons .= '</div>';

                    return $buttons;

                })

                ->addIndexColumn()
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->orderColumn('created_by', function ($query, $order) {
                    $query->orderBy('created_by', $order);
                })

                ->rawColumns(['action'])

                ->make(true);

        } catch (\Exception $e){

            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ],500);

        }
    }

    public function edit($id)
    {
        $item = NoteTitleModel::find($id);

        if(!$item){

            return response()->json([
                'status'=>'error',
                'message'=>'Note Title not found'
            ],404);

        }

        return response()->json([
            'status'=>'success',
            'data'=>$item
        ]);
    }

    public function storeOrUpdate(Request $request)
    {
        DB::beginTransaction();

        try{

            $ids = $request->input('note_title_id');
            $titles = $request->input('note_title');

            if (!is_array($titles)) {
                $titles = ($titles !== null && $titles !== '') ? [$titles] : [];
            }

            if (empty($titles)) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'At least one Note Title is required.',
                    'errors'  => [
                        'note_title.0' => ['At least one Note Title is required.']
                    ]
                ], 422);
            }

            if(!is_array($ids)){
                $ids = $ids ? [$ids] : [];
            }

            $createdItems = [];

            $created = false;
            $updated = false;

            foreach($titles as $index=>$title){
                //$title = mb_strtoupper(trim($title), 'UTF-8');
                $title = trim($title);
                if(empty($title)){

                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Note Title is required.',
                        'errors'  => [
                            "note_title.$index"=>[
                                "Note Title is required."
                            ]
                        ]
                    ],422);

                }

                $id = $ids[$index] ?? null;

                $exists = NoteTitleModel::where('note_title',$title)
                    ->where('status',0)
                    ->when($id,function($q) use($id){
                        $q->where('note_title_id','!=',$id);
                    })
                    ->exists();

                if($exists){

                    return response()->json([
                        'status'  => 'error',
                        'message' => 'This Note Title already exists.',
                        'errors'  => [
                            "note_title.$index"=>[
                                "This Note Title already exists."
                            ]
                        ]
                    ],422);

                }

                if($id){

                    $item = NoteTitleModel::find($id);

                    if($item){

                        $item->update([
                            'note_title'=>$title,
                            'updated_by'=>getUpdatedBy(),
                            'updated_at'=>now()
                        ]);

                        $updated = true;

                    }

                }else{

                    $newItem = NoteTitleModel::create([
                        'note_title'=>$title,
                        'status'=>0,
                        'created_by'=>getCreatedBy(),
                        'created_at'=>now()
                    ]);

                    $createdItems[] = $newItem;

                    $created = true;

                }

            }

            DB::commit();

            if($created && $updated){

                $message = 'Note Titles updated and created successfully.';

            }elseif($updated){

                $message = 'Note Title updated successfully.';

            }elseif($created){

                $message = 'Note Title created successfully.';

            }else{

                $message = 'No changes were made.';

            }

            return response()->json([
                'status'=>'success',
                'message'=>$message,
                'data'=>$createdItems
            ]);

        }catch(\Exception $e){

            DB::rollBack();

            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ],500);

        }
    }

    public function destroy(Request $request)
    {
        try{

            $item = NoteTitleModel::find($request->note_title_id);

            if(!$item){

                return response()->json([
                    'status'=>'error',
                    'message'=>'Note Title not found'
                ]);

            }

            $item->update([
                'status'=>1,
                'updated_by'=>getUpdatedBy(),
                'updated_at'=>now()
            ]);

            return response()->json([
                'status'=>'success',
                'message'=>'Note Title deleted successfully.'
            ]);

        }catch(\Exception $e){

            return response()->json([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);

        }
    }

}
