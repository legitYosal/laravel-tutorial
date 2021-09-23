<?php 

namespace App\Traits;
use Illuminate\Http\UploadedFile;

trait FakeImage {
    protected function randomFakeImage() {
        $image_name = $this->faker->unique()->word().'.jpeg';
        return UploadedFile::fake()->image(
            $image_name
        );
    }
}