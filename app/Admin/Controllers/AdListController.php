<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Models\AdList; 

class AdListController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Ad List';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new AdList());
        $grid->model()->latest();
        $grid->column('id', __('Id'));
        $grid->column('product_id', __('Product ID'));
        $grid->column('user_id', __('User ID'));
        $grid->column('img', __('Image'))->image('', 60, 60);
        $grid->column('data', __('Data'))->style('max-width:200px;word-break:break-all;')->display(function ($val) {
            return substr($val, 0, 30);
        });
        $grid->column('created_at', __('Created_at'));
        $grid->column('updated_at', __('Updated_at'));

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
        $show = new Show(AdList::findOrFail($id));

        // You can customize the show page if needed.

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new AdList());
        $form->number('product_id', __('Product ID'));
        $form->number('user_id', __('User ID'));
        $form->image('img', __('Image'))->uniqueName();
        $form->textarea('data', __('Data'));
        
        // Add other fields as needed

        return $form;
    }
}
