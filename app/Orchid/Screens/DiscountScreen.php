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
use App\Models\Discount;

class DiscountScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'discounts' => Discount::filters()->defaultSort('id', 'desc')->paginate(10)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Discounts';
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
                ->modal('discountModal')
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
            Layout::table('discounts', [
                TD::make('id')->sort(),
                TD::make('name')->sort(),
                TD::make('description')->sort()
                    ->render(function (Discount $discount) {
                        $description = (strlen($discount->description) > 53) ? substr($discount->description, 0, 50) . '...' : $discount->description;
                        return $description;
                    }),
                TD::make('location')->sort(),
                TD::make('points')->sort(),
                TD::make('photo')
                    ->render(function (Discount $discount) {
                        $url = url('storage/' . $discount->photo);
                        return "<img src=\"$url\" alt=\"Discount photo\" style=\"max-width: 100px; max-height: 80px;\">";
                    }),

                TD::make('Actions')
                    ->alignRight()
                    ->render(function (Discount $discount) {
                        return Button::make('Delete Discount')
                            ->confirm('After deleting, the discount will be gone forever.')
                            ->method('delete', ['discount' => $discount->id]);
                    }),
            ]),

            Layout::modal('discountModal', Layout::rows([
                Input::make('discount.name')
                    ->title('Name')
                    ->placeholder('Enter discount name')
                    ->help('The name of the discount to be created.'),

                TextArea::make('discount.description')
                    ->title('Description')
                    ->rows(5)
                    ->maxlength(1024)
                    ->placeholder('Enter discount description')
                    ->help('The description of the discount to be created.'),

                Input::make('discount.location')
                    ->title('Location')
                    ->placeholder('Enter discount location')
                    ->help('The location of the discount to be created.'),

                Input::make('discount.points')
                    ->type('number')
                    ->title('Points')
                    ->placeholder('Enter discount points')
                    ->help('The number of points required for the discount.'),

                Upload::make('discount.photo')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->title('Photo')
                    ->placeholder('Upload discount photo')
                    ->help('The photo of the discount to be created.'),
            ]))
                ->title('Create discount')
                ->applyButton('Add Discount'),
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
            'discount.name' => 'required',
            'discount.description' => 'required',
            'discount.location' => 'required',
            'discount.points' => 'required',
            'discount.photo' => 'required',
        ]);

        $attachment = Attachment::find($request->input('discount.photo'))->first();

        $discount = new Discount();
        $discount->name = $request->input('discount.name');
        $discount->description = $request->input('discount.description');
        $discount->location = $request->input('discount.location');
        $discount->points = $request->input('discount.points');
        $discount->photo = $attachment->path . $attachment->name . '.' . $attachment->extension;
        $discount->save();
    }

    /**
     * @param Discount $discount
     *
     * @return void
     */
    public function delete(Discount $discount)
    {
        $discount->delete();
    }
}
