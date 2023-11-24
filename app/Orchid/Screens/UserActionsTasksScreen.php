<?php

namespace App\Orchid\Screens;

use App\Models\User;
use App\Models\Task;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserActionsTasksScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Mark task as completed by user';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([
                Select::make('user')
                    ->title('Select user')
                    ->fromModel(User::class, 'name'),

                Select::make('task')
                    ->title('Select task')
                    ->fromModel(Task::class, 'name'),

                Button::make('Mark as completed')
                    ->icon('check')
                    ->method('mark'),
            ])
        ];
    }

    public function mark(Request $request)
    {
        $userId = $request->input('user');
        $taskId = $request->input('task');

        $userTask = new UserTask();
        $userTask->user_id = $userId;
        $userTask->task_id = $taskId;
        $userTask->save();

        $user = User::find($userId);
        $user->points += Task::find($taskId)->points;
        $user->save();

        Toast::success("Successfully marked task as completed");
    }
}
