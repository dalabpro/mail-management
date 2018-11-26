<?php

namespace Kgregorywd\MailManagement\Http\Controllers\Backend;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Kgregorywd\MailManagement\Models\MailBox;
use MailManagement;

class MailManagementController extends BackendController
{

    public function __construct(Request $request, MailBox $model)
    {
        parent::__construct($request, $model);

        $this->middleware(function ($request, $next) {

            $this->setCollect([
                'titleIndex' => trans("MailManagement::{$this->prefix}.{$this->getCollect('type')}.title_index"),
                'titleCreate' => trans("MailManagement::{$this->prefix}.{$this->getCollect('type')}.title_create"),
                'titleShow' => trans("MailManagement::{$this->prefix}.{$this->getCollect('type')}.title_show"),
                'titleEdit' => trans("MailManagement::{$this->prefix}.{$this->getCollect('type')}.title_edit"),
                'viewPath' => $this->getCollect('viewPath'),
            ])->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleIndex'),
                        'url' => route("backend.{$this->getCollect('type')}.index")
                    ],
                ]),
            ]);

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @param MailBox $model
     * @return \Illuminate\Http\Response
     */
    public function index(MailBox $model)
    {

        $this
            ->setCollect('model', $model)
            ->setCollect('models', $model->paginate(25))
            ->setCollect('breadcrumbs', (String)view()->make('backend.ajax.breadcrumb', $this->getCollect())->render());

        return view('MailManagement::' . $this->getCollect('prefix') . '.' . $this->getCollect('viewPath') . '.' . __FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param MailBox $model
     * @return \Illuminate\Http\Response
     */
    public function create(MailBox $model)
    {
        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleCreate'),
                        'url' => route("backend.{$this->getCollect('type')}." . __FUNCTION__)
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('breadcrumbs', (String)view()->make('backend.ajax.breadcrumb', $this->getCollect())->render());

        return view('MailManagement::' . $this->getCollect('prefix') . '.' . $this->getCollect('viewPath') . '.' . __FUNCTION__, $this->getCollect());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param MailBox $model
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, MailBox $model)
    {
        $requestData = $request->all();

        $validator = \Validator::make($requestData, $this->required);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model = $model->create($requestData);

        return redirect(route("backend.{$this->getCollect('type')}.edit", $model));
    }

    /**
     * Display the specified resource.
     *
     * @param MailBox $model
     * @return \Illuminate\Http\Response
     */
    public function show(MailBox $model)
    {
        return view('MailManagement::' . $this->getCollect('prefix') . '.' . $this->getCollect('viewPath') . '.' . __FUNCTION__, $this->getCollect());
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param MailBox $model
     * @return \Illuminate\Http\Response
     */
    public function edit(MailBox $model)
    {
        $this
            ->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleEdit'),
                        'url' => route("backend.{$this->getCollect('type')}." . __FUNCTION__, ['language' => $model->id])
                    ],
                ]),
            ])
            ->setCollect('model', $model)
            ->setCollect('breadcrumbs', (String)view()->make('backend.ajax.breadcrumb', $this->getCollect())->render());

        return view('MailManagement::' . $this->getCollect('prefix') . '.' . $this->getCollect('viewPath') . '.' . __FUNCTION__, $this->getCollect());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param MailBox $model
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MailBox $model)
    {
        $requestData = $request->all();

        $validator = \Validator::make($requestData, $this->required);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $model->update($requestData);

        return redirect(route("backend.{$this->getCollect('type')}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param MailBox $model
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, MailBox $model)
    {
        $result = [];
        $data = [];
        $ids = $request->input('ids');
        if (!empty($ids)) {
            if (is_array($ids) && sizeof($ids) > 0) {
                foreach($ids as $k => $item) {
                    try{
                        $i = $model->find($item);
                        $i = $i ? $i->delete() : false;
                        if ($i) {
                            $data[] = [
                                'success' => true,
                                'message' => 'Запись успешно удалена!',
                            ];
                        } else {
                            $data[] = [
                                'success' => false,
                                'message' => 'Возникла ошибка. Запись могла быть не удалена!',
                            ];
                        }
                    } catch (\Exception $e) {
                        $data[] = [
                            'success' => false,
                            'message' => $e->getMessage(),
                        ];
                    }
                }
            }

            $result['data'] = $data;

            return response()->json($result);
        }
    }

    public function parse($clientId)
    {
        MailManagement::parse($clientId);

        return redirect()->back();
    }
}
