<?php

namespace App\Http\Controllers;

use App\Facades\FileManager;
use App\Facades\SemanticManager;
use App\Models\Book;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::where('user_id', Auth::id())->paginate(10);
        return Inertia::render('App/Books/Index', ['books' => $books]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create([
            'user_id' => Auth::id(),
            ...$request->only(
                'title',
                'volume',
                'edition',
                'pages',
                'isbn',
                'author',
                'genre',
                'publisher',
                'description',
                'year',
            ),
        ]);

        if($request->hasFile('thumbnail')){
            $book->thumbnail_id = FileManager::upload($request->file('thumbnail'), 'thumbnails')->id;
        }

        if($request->hasFile('pdf')){
            $book->pdf_id = FileManager::upload($request->file('pdf'), 'pdfs')->id;
        }

        $book->save();

        return response()->json($book, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $this->authorize('view', $book);
        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $this->authorize('update', $book);
        $book->update($request->only(
            'title',
            'volume',
            'edition',
            'pages',
            'isbn',
            'author',
            'genre',
            'publisher',
            'description',
            'year',
        ));

        if($request->hasFile('thumbnail')){
            $book->thumbnail_id = FileManager::upload($request->file('thumbnail'), 'thumbnails')->id;
        }

        if($request->hasFile('pdf')){
            $book->pdf_id = FileManager::upload($request->file('pdf'), 'pdfs')->id;
        }

        $book->save();
        
        return response()->json($book);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);
        $book->deleteWithFiles();

        return response()->json(['message' => 'Livro excluído com sucesso.']);
    }

    public function uploadFile(Request $request, Book $book){
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $book->pdf_id = FileManager::upload($request->file('file'), 'pdfs')->id;
        $book->save();

        return response()->json(['message' => 'Arquivo enviado com sucesso.']);
    }

    public function advancedSearch(Request $request){
        $request->validate([
            'query' => 'required|string|min:3',
        ]);

        $results = SemanticManager::advanceSearch($request->query('query'));

        return response()->json($results);
    }
}
