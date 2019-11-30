<?php

namespace App\Admin\Controllers;

use App\AnswerQuiz;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class AnswerQuizController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\AnswerQuiz';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AnswerQuiz);

        $grid->column('id', __('Id'));
        $grid->column('question_id', __('Question id'));
        $grid->column('text', __('Text'));
        $grid->column('correct_one', __('Correct one'));
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
        $show = new Show(AnswerQuiz::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('question_id', __('Question id'));
        $show->field('text', __('Text'));
        $show->field('correct_one', __('Correct one'));
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
        $form = new Form(new AnswerQuiz);

        $form->number('question_id', __('Question id'));
        $form->text('text', __('Text'));
        $form->switch('correct_one', __('Correct one'));

        return $form;
    }
}
