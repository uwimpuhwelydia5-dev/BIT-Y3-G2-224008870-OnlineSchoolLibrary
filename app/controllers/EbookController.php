



<?php

class EbookController
{
    private $ebookModel;

    public function __construct($ebookModel)
    {
        $this->ebookModel = $ebookModel;
    }

    public function index()
    {
        return $this->ebookModel->getAll();
    }

    public function search($keyword)
    {
        return $this->ebookModel->search($keyword);
    }

    public function show($id)
    {
        return $this->ebookModel->findById($id);
    }

    public function create($title, $author, $category, $file_url, $copies)
    {
        return $this->ebookModel->create(
            $title,
            $author,
            $category,
            $file_url,
            $copies
        );
    }

    public function update($id, $title, $author, $category, $file_url, $copies)
    {
        return $this->ebookModel->update(
            $id,
            $title,
            $author,
            $category,
            $file_url,
            $copies
        );
    }

    public function delete($id)
    {
        return $this->ebookModel->delete($id);
    }

    public function borrow($ebook_id, $user_id)
    {
        return $this->ebookModel->borrow($ebook_id, $user_id);
    }

    public function returnBook($borrow_id)
    {
        return $this->ebookModel->returnBook($borrow_id);
    }

    public function updateReadingProgress($borrow_id, $user_id, $pages_read)
    {
        return $this->ebookModel->updateReadingProgress(
            $borrow_id,
            $user_id,
            $pages_read
        );
    }
}
