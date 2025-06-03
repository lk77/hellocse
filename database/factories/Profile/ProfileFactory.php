<?php

namespace Database\Factories\Profile;

use App\Enums\Profile\ProfileStatus;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Safe\Exceptions\ImageException;

use function Safe\imagecreatetruecolor;
use function Safe\imagedestroy;
use function Safe\imagefill;
use function Safe\imagejpeg;
use function Safe\imagestring;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $image = $this->image();

        return [
            'firstname'           => $this->faker->firstName(),
            'lastname'            => $this->faker->lastName(),
            'image_original_name' => $image->originalName,
            'image_name'          => $image->name,
            'status'              => ProfileStatus::active->name,
            'user_id'             => User::factory(),
        ];
    }

    /**
     * @return object{originalName: string, name: string}
     *
     * @throws ImageException
     */
    private function image(): object
    {
        // Generate a file name
        $originalName = $this->faker->colorName().'-'.$this->faker->word().'.jpeg';
        $name = sha1($originalName).'.jpeg';

        // The path of the file
        $path = '/tmp/'.$name;

        // Create a blank image and add some content
        $image = imagecreatetruecolor(200, 200);
        $bgColor = imagecolorallocate($image, 75, 75, 75);

        // Set the background color
        imagefill($image, 0, 0, 0 + $bgColor);

        // Add some text
        $color = imagecolorallocate($image, 225, 225, 225);
        imagestring($image, 25, 50, 200, $this->faker->sentence(), $color | 0);

        // Save the image as jpeg
        imagejpeg($image, $path);

        // Free the memory
        imagedestroy($image);

        return (object) compact('originalName', 'name');
    }
}
