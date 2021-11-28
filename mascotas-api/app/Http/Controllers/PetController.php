<?php

namespace App\Http\Controllers;

use App\Dtos\PetDTO;
use App\Http\Requests\Pets\AddRequest;
use App\Repositories\ImageRepository;
use App\Repositories\PetRepository;
use App\Repositories\SexRepository;
use App\Repositories\SpeciesRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PetController extends Controller
{
    protected $sexRepository;
    protected $speciesRepository;
    protected $petRepository;
    protected $imageRepository;

    public function __construct(
        SexRepository     $sexRepository,
        SpeciesRepository $speciesRepository,
        PetRepository     $petRepository,
        ImageRepository   $imageRepository
    )
    {
        $this->sexRepository = $sexRepository;
        $this->speciesRepository = $speciesRepository;
        $this->petRepository = $petRepository;
        $this->imageRepository = $imageRepository;
    }

    /**
     * Returns the data necessary for the creation of the add pet form.
     *
     * @return JsonResponse
     */
    public function addForm(): JsonResponse
    {
        $sexes = $this->sexRepository->getAll();
        $species = $this->speciesRepository->getAll();

        return response()->json([
            'data' => compact('sexes', 'species'),
        ]);
    }

    /**
     * Agrega una mascota a la base de datos
     *
     * @param AddRequest $request
     * @return JsonResponse
     */
    public function addPet(AddRequest $request): JsonResponse
    {
        $photo = $request->get('photo');
        $image_id = $request->get('species_id');

        if ($photo) {
            $image = $this->imageRepository->uploadImage($photo, 'pets/', 'Mascota ' . $request->get('name'));
            $image_id = $image->id;
        }

        $dto = new PetDTO();
        $dto->loadFromArray($request->input());
        $dto->set_image_id($image_id);
        $pet = $this->petRepository->updateOrCreate($dto, 1);

        return response()->json([
            'success' => true,
            'data' => $pet,
        ]);
    }

    /**
     * Updates or removes observation from a pet
     *
     * @param Request $request
     * @param $pet_id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function updateObservation(Request $request, $pet_id): JsonResponse
    {
        switch ($request->input('action')) {
            case 'update':
                $this->validateObservation($request->all());
                $observation = $request->get('data')['observation'];
                break;

            case 'delete':
                $observation = '';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'msg' => 'Invalid request action'
                ], 400);
        }

        try {
            $pet = $this->petRepository->updateObservation($pet_id, $observation);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'errors' => ['pet_id' => 'No se encontró la mascota relacionada']
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pet,
        ]);
    }

    /**
     * Returns all user's pets
     *
     * @return JsonResponse
     */
    public function getOwnerPets(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'pets' => $this->petRepository->getPetsByUser(1)
            ],
        ]);
    }

    /**
     * Performs specific validation for the observation field
     *
     * @param $request_data
     * @throws ValidationException
     */
    private function validateObservation($request_data)
    {
        Validator::make(
            $request_data,
            ['data.observation' => 'required'],
            ['data.observation.required' => 'Por favor ingresá una observación']
        )->validate();
    }
}