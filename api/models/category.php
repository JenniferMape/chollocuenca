<?php

class Category
{

    public function getAllCategories()
    {
        $categories = ORM::for_table('categories')->find_many();
        return $this->convertCollection($categories);
    }

    public function getCategory($id)
    {
        $category = ORM::for_table('categories')->find_one($id);
        return $category ? $this->convertObj($category) : null;
    }

    public function addCategory($data)
    {

        if (empty($data['name_category']) || empty($data['description_category'])) {
            return false;
        }

        $category = ORM::for_table('categories')->create();
        $category->name_category = $data['name_category'];
        $category->description_category = $data['description_category'];
        $category->save();

        return $this->convertObj($category);
    }

    public function updateCategory($data)
    {
        $category = ORM::for_table('categories')->find_one($data['id']);

        if ($category) {

            $category->name_category = $data['name_category'] ?? $category->name_category;
            $category->description_category =  $data['description_category'] ?? $category->description_category;


            $category->save();

            return true;
        } else {
            return false;
        }
    }

    public function deleteCategory($id)
    {
        $category = ORM::for_table('categories')->find_one($id);
        if ($category) {
            $category->delete();
            return true;
        } else {
            return false;
        }
    }

    private function convertObj($obj)
    {
        return [
            'id' => $obj->id ?? null,
            'name_category' => $obj->name_category ?? null,
            'description_category' => $obj->description_category ?? null,
            'created_category' => $obj->created_category ?? null,
            'updated_category' => $obj->updated_category ?? null
        ];
    }
    private function convertCollection($collection)
    {
        $result = [];
        foreach ($collection as $item) {
            $result[] = $this->convertObj($item);
        }
        return $result;
    }
}
