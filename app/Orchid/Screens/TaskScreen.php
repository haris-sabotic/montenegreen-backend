<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\TD;
use Orchid\Attachment\Models\Attachment;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'tasks' => Task::filters()->defaultSort('id', 'desc')->paginate(10)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Tasks';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add')
                ->modal('taskModal')
                ->method('create')
                ->icon('bs.plus-circle'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('tasks', [
                TD::make('id')->sort(),
                TD::make('name')->sort(),
                TD::make('description')->sort()
                    ->render(function (Task $task) {
                        $description = (strlen($task->description) > 53) ? substr($task->description, 0, 50) . '...' : $task->description;
                        return $description;
                    }),
                TD::make('location')->sort(),
                TD::make('points')->sort(),
                TD::make('photo')
                    ->render(function (Task $task) {
                        $url = url('storage/' . $task->photo);
                        return "<img src=\"$url\" alt=\"Task photo\" style=\"max-width: 100px; max-height: 80px;\">";
                    }),

                TD::make('Actions')
                    ->alignRight()
                    ->render(function (Task $task) {
                        return Button::make('Delete Task')
                            ->confirm('After deleting, the task will be gone forever.')
                            ->method('delete', ['task' => $task->id]);
                    }),
            ]),

            Layout::modal('taskModal', Layout::rows([
                Input::make('task.name')
                    ->title('Name')
                    ->placeholder('Enter task name')
                    ->help('The name of the task to be created.'),

                TextArea::make('task.description')
                    ->title('Description')
                    ->rows(5)
                    ->maxlength(1024)
                    ->placeholder('Enter task description')
                    ->help('The description of the task to be created.'),

                Input::make('task.location')
                    ->title('Location')
                    ->placeholder('Enter task location')
                    ->help('The location of the task to be created.'),

                Input::make('task.points')
                    ->type('number')
                    ->title('Number of points')
                    ->placeholder('Enter number of points')
                    ->help('The number of points given to the user upon completion of the task to be created.'),

                Upload::make('task.photo')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->title('Photo')
                    ->placeholder('Upload task photo')
                    ->help('The photo of the task to be created.'),
            ]))
                ->title('Create Task')
                ->applyButton('Add Task'),
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        $request->validate([
            'task.name' => 'required',
            'task.description' => 'required',
            'task.location' => 'required',
            'task.points' => 'required',
            'task.photo' => 'required',
        ]);

        $attachment = Attachment::find($request->input('task.photo'))->first();

        $task = new Task();
        $task->name = $request->input('task.name');
        $task->description = $request->input('task.description');
        $task->location = $request->input('task.location');
        $task->photo = $attachment->path . $attachment->name . '.' . $attachment->extension;
        $task->points = $request->input('task.points');
        $task->save();
    }

    /**
     * @param Task $task
     *
     * @return void
     */
    public function delete(Task $task)
    {
        $task->delete();
    }
}
