<?php

namespace App\Orchid\Screens;

use App\Models\Blogpost;
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

class BlogpostScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'blogposts' => Blogpost::filters()->defaultSort('id', 'desc')->paginate(10)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Blog posts';
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
                ->modal('blogpostModal')
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
            Layout::table('blogposts', [
                TD::make('id')->sort(),
                TD::make('name')->sort(),
                TD::make('description')
                    ->render(function (Blogpost $blogpost) {
                        $description = (strlen($blogpost->description) > 53) ? substr($blogpost->description, 0, 50) . '...' : $blogpost->description;
                        return $description;
                    }),
                TD::make('youtube'),
                TD::make('photo')
                    ->render(function (Blogpost $blogpost) {
                        $url = url('storage/' . $blogpost->photo);
                        return "<img src=\"$url\" alt=\"Blog post photo\" style=\"max-width: 100px; max-height: 80px;\">";
                    }),

                TD::make('Actions')
                    ->alignRight()
                    ->render(function (Blogpost $blogpost) {
                        return Button::make('Delete blog post')
                            ->confirm('After deleting, the blog post will be gone forever.')
                            ->method('delete', ['blogpost' => $blogpost->id]);
                    }),
            ]),

            Layout::modal('blogpostModal', Layout::rows([
                Input::make('blogpost.name')
                    ->title('Name')
                    ->placeholder('Enter blogpost name')
                    ->help('The name of the blogpost to be created.'),

                TextArea::make('blogpost.description')
                    ->title('Description')
                    ->rows(5)
                    ->maxlength(1024)
                    ->placeholder('Enter blogpost description')
                    ->help('The description of the blogpost to be created.'),

                Input::make('blogpost.youtube')
                    ->title('Youtube url')
                    ->placeholder('Enter blogpost youtube url')
                    ->help('The youtube url of the blogpost to be created.'),

                Upload::make('blogpost.photo')
                    ->acceptedFiles('image/*')
                    ->maxFiles(1)
                    ->title('Photo')
                    ->placeholder('Upload blogpost photo')
                    ->help('The photo of the blogpost to be created.'),
            ]))
                ->title('Create blog post')
                ->applyButton('Add blog post'),
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
            'blogpost.name' => 'required',
            'blogpost.description' => 'required',
            'blogpost.youtube' => 'required',
            'blogpost.photo' => 'required',
        ]);

        $attachment = Attachment::find($request->input('blogpost.photo'))->first();

        $blogpost = new Blogpost();
        $blogpost->name = $request->input('blogpost.name');
        $blogpost->description = $request->input('blogpost.description');
        $blogpost->youtube = $request->input('blogpost.youtube');
        $blogpost->photo = $attachment->path . $attachment->name . '.' . $attachment->extension;
        $blogpost->save();
    }

    /**
     * @param Blogpost $blogpost
     *
     * @return void
     */
    public function delete(Blogpost $blogpost)
    {
        $blogpost->delete();
    }
}
