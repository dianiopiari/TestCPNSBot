<?php

namespace App\Admin\Controllers;

use App\TipeQuestion;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TipeQuestionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\TipeQuestion';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new TipeQuestion);

        $grid->column('id', __('Id'));
        $grid->column('tipe', __('Tipe'));
        $states = [
            'off' => ['value' => 0, 'text' => 'enable', 'color' => 'success'],
            'on' => ['value' => 1, 'text' => 'disable', 'color' => 'danger'],
        ];
        $grid->column('status', __('status'))->switch($states);
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(TipeQuestion::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('tipe', __('Tipe'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new TipeQuestion);

        $form->text('tipe', __('Tipe'));
        //$form->select('tipe', __('Tipe'))->options([1 => 'foo', 2 => 'bar', 'val' => 'Option name']);
        $states = [
            'off' => ['value' => 0, 'text' => 'enable', 'color' => 'success'],
            'on' => ['value' => 1, 'text' => 'disable', 'color' => 'danger'],
        ];

        $form->switch('status', __('Status'))->states($states);

        return $form;
    }
}
