<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Repositories\UserTypeRepository;
use App\Repositories\ImageRepository;
use App\Http\Requests\Users\Professionals\CreateRequest;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    protected $userRepository;
    protected $userTypeRepository;
    protected $imageRepository;


    public function __construct(UserRepository $userRepository, UserTypeRepository $userTypeRepository, ImageRepository $imageRepository) {
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * Returns all users with role as professional
     *
     * @return JsonResponse
     */
    public function getProfessionals(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                // TODO: find central place to define user type ids - professionals / admins / not professionals
                'users' => $this->userRepository->getUsersByTypes(array(5, 6, 7, 8))
            ],
        ]);
    }

    /**
     * Create a new professional user in the database
     *
     * @param CreateRequest $request
     * @return JsonResponse
     */
    public function createProfessional(CreateRequest $request): JsonResponse
    {
        $photo = $request->get('photo');
        $profile_img_id = null;

        if ($photo) {
            $image = $this->imageRepository->uploadImage($photo, 'users/profile/', 'Perfil ' . $request->get('first_name') . ' ' . $request->get('last_name'));
            $profile_img_id = $image->id;
        }

        $user = $this->userRepository->updateOrCreate(
            $request->get('id'),
            $request->get('first_name'),
            $request->get('last_name'),
            $request->get('email'),
            $request->get('email_verified_at'),
            false,
            #$request->get('email_visible'),
            $request->get('password'),
            $request->get('country'),
            $request->get('state'),
            $request->get('city'),
            $request->get('postal_code'),
            $request->get('district'),
            $request->get('street'),
            $request->get('house_number'),
            $request->get('apartment'),
            $request->get('latitude'),
            $request->get('longitude'),
            $request->get('dni'),
            $request->get('description'),
            $request->get('whatsapp'),
            $request->get('instagram'),
            $request->get('facebook'),
            $request->get('web'),
            $request->get('verified'),
            $profile_img_id
        );

        $user->userTypes()->sync($request->get('user_types'));

        return  response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Returns the data necessary for the creation of the create professional form
     *
     * @return JsonResponse
     */
    public function createFormProfessional(): JsonResponse
    {
        $user_types = $this->userTypeRepository->getProfessionals();

        return response()->json([
            'data' => compact('user_types'),
        ]);
    }
}
