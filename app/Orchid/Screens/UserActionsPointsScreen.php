<?php

namespace App\Orchid\Screens;

use App\Models\User;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class UserActionsPointsScreen extends Screen
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
        return 'Add points to user';
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

                Input::make('points')
                    ->type('number')
                    ->title('Number of points'),

                Button::make('Add points')
                    ->icon('plus')
                    ->method('add'),
            ])
        ];
    }

    public function add(Request $request)
    {
        $userId = $request->input('user');
        $points = $request->input('points');


        $user = User::find($userId);
        $user->points += $points;
        $user->save();

        Toast::success("Successfully added points to user");
    }
}
