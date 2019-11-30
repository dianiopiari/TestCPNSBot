<?php

namespace App\Admin\Controllers;

use App\QuestionQuiz;
use App\TipeQuestion;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class QuestionQuizController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\QuestionQuiz';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new QuestionQuiz);

        $grid->column('id', __('Id'));
        $grid->column('tipe.tipe', __('Tipe'));
        $grid->column('text', __('Text'));
        $grid->column('points', __('Points'));
        $grid->answers()->display(function ($answers) {
            $answers = array_map(function ($answers) {
                return "<span class='label label-success'>{$answers['text']}</span>";
            }, $answers);
            return join('&nbsp;', $answers);
        });
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
        $show = new Show(QuestionQuiz::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('text', __('Text'));
        $show->field('points', __('Points'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new QuestionQuiz);
        $form->select('tipe_id',' Tipe Pertanyaa')->options(TipeQuestion::get()->pluck('tipe','id')->toArray());
        $form->text('text', __('Text'));
        $form->number('points', __('Points'));
        $form->hasMany('answers', function (Form\NestedForm $form) {
            $form->text('text', 'Jawaban');
            $form->select('correct_one', 'Jawab Benar')->options([0 => 'Salah', 1 => 'Benar']);
        });
        return $form;
    }
}
