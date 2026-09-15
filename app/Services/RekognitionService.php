<?php

namespace App\Services;

use Aws\Exception\AwsException;
use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Log;


class RekognitionService
{
    //  private $rekognitionClient;
    private $region;
    private $collectionId;

    protected RekognitionClient $client;
    // protected string $collectionId;
    public function __construct()
    {
        $this->client = new RekognitionClient([
            'region'      => env('AWS_DEFAULT_REGION', 'ap-south-1'),
            'version'     => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'http' => [
                'verify' => env('APP_ENV') === 'local' ? false : true
            ]
        ]);

        $this->collectionId = env('REKOGNITION_COLLECTION_ID', 'staff_faces');
        $this->ensureCollectionExists();
    }

    protected function ensureCollectionExists(): void
    {
        try {
            $this->client->describeCollection(['CollectionId' => $this->collectionId]);
        } catch (AwsException $e) {
            if ($e->getAwsErrorCode() === 'ResourceNotFoundException') {
                $this->client->createCollection(['CollectionId' => $this->collectionId]);
            } else {
                Log::error('AWS Collection Error: ' . $e->getMessage());
            }
        }
    }

    public function indexFace(string $imageBytes, string $staffId): ?string
    {
        try {
            $result = $this->client->indexFaces([
                'CollectionId'        => $this->collectionId,
                'Image'               => ['Bytes' => $imageBytes],
                'ExternalImageId'     => (string) $staffId,
                'DetectionAttributes' => ['DEFAULT'],
                'MaxFaces'            => 1,
            ]);

            $faceRecords = $result->get('FaceRecords');
            return ! empty($faceRecords) ? $faceRecords[0]['Face']['FaceId'] : null;
        } catch (AwsException $e) {
            Log::error('AWS Rekognition Index Error: ' . $e->getMessage());
            throw new \Exception('Failed to register face with AWS Rekognition.');
        }
    }

    public function validateFaceImage(string $imageBytes): void
    {
        try {
            // 1. Detect Labels to reject animals, objects, cartoons, documents, screenshots, logos
            $labelResult = $this->client->detectLabels([
                'Image' => ['Bytes' => $imageBytes],
                'MaxLabels' => 20,
                'MinConfidence' => 70.0,
            ]);

            $invalidLabels = ['Animal', 'Pet', 'Dog', 'Cat', 'Document', 'Text', 'Screenshot', 'Logo', 'Cartoon', 'Animation', 'Avatar', 'Illustration', 'Graphics', 'Toy', 'Poster', 'Art', 'Drawing', 'Vehicle', 'Furniture'];

            foreach ($labelResult->get('Labels') as $label) {
                if (in_array($label['Name'], $invalidLabels)) {
                    throw new \Exception("Invalid image: Detected {$label['Name']}. Please upload a real human face.");
                }
            }

            // 2. Detect Faces for quality and exact count
            $faceResult = $this->client->detectFaces([
                'Image' => ['Bytes' => $imageBytes],
                'Attributes' => ['ALL'],
            ]);

            $faces = $faceResult->get('FaceDetails');

            if (empty($faces)) {
                throw new \Exception("No face detected in the image.");
            }

            if (count($faces) > 1) {
                throw new \Exception("Multiple faces detected. Please upload an image with exactly one person.");
            }

            $face = $faces[0];

            if ($face['Confidence'] < 95.0) {
                throw new \Exception("Face confidence is too low. Please upload a clear image of a human face.");
            }

            // Quality checks
            $quality = $face['Quality'] ?? [];
            $brightness = $quality['Brightness'] ?? 100;
            $sharpness = $quality['Sharpness'] ?? 100;

            if ($brightness < 40) {
                throw new \Exception("The image is too dark. Please upload a well-lit image.");
            }
            if ($sharpness < 40) {
                throw new \Exception("The image is blurry. Please upload a clearer image.");
            }

            // Pose checks for front-facing
            $pose = $face['Pose'] ?? [];
            if (abs($pose['Yaw'] ?? 0) > 30 || abs($pose['Pitch'] ?? 0) > 30 || abs($pose['Roll'] ?? 0) > 30) {
                throw new \Exception("The face is not front-facing. Please look straight at the camera.");
            }

            // Mask/Covering checks
            $sunglasses = $face['Sunglasses'] ?? [];
            if (($sunglasses['Value'] ?? false) && ($sunglasses['Confidence'] ?? 0) > 80) {
                throw new \Exception("Please remove sunglasses or face coverings.");
            }

            $faceOccluded = $face['FaceOccluded'] ?? [];
            if (($faceOccluded['Value'] ?? false) && ($faceOccluded['Confidence'] ?? 0) > 80) {
                throw new \Exception("The face is partially covered or masked. Please show your full face clearly.");
            }

            // Bounding box size check (partially visible)
            $boundingBox = $face['BoundingBox'] ?? [];
            $width = $boundingBox['Width'] ?? 0;
            $height = $boundingBox['Height'] ?? 0;

            if ($width < 0.1 || $height < 0.1) {
                throw new \Exception("The face is too small or partially visible in the image.");
            }

        } catch (AwsException $e) {
            Log::error('AWS Rekognition Validation Error: ' . $e->getMessage());
            throw new \Exception('Failed to process image for face validation. ' . $e->getMessage());
        }
    }

    public function searchFace(string $imageBytes, float $threshold = 90.0): ?array
    {
        try {
            $result = $this->client->searchFacesByImage([
                'CollectionId'       => $this->collectionId,
                'Image'              => ['Bytes' => $imageBytes],
                'FaceMatchThreshold' => $threshold,
                'MaxFaces'           => 1,
            ]);

            $matches = $result->get('FaceMatches');
            if (! empty($matches)) {
                return [
                    'FaceId'          => $matches[0]['Face']['FaceId'],
                    'ExternalImageId' => $matches[0]['Face']['ExternalImageId'] ?? null,
                    'Similarity'      => $matches[0]['Similarity'],
                ];
            }
            return null;
        } catch (AwsException $e) {
            Log::error('AWS Rekognition Search Error: ' . $e->getMessage());
            throw new \Exception('Failed to process image for recognition. AWS Rekognition error occurred..');
        }
    }

    public function listAllFaces()
    {
        try {
            $result = $this->client->listFaces([
                'CollectionId' => $this->collectionId,
                'MaxResults'   => 50
            ]);

            return $result->get('Faces') ?? [];
        } catch (AwsException $e) {
            \Illuminate\Support\Facades\Log::error('AWS List Faces Error: ' . $e->getMessage());
            return [];
        }
    }

    public function deleteFace(string $faceId): bool
    {
        try {
            $this->client->deleteFaces([
                'CollectionId' => $this->collectionId,
                'FaceIds' => [$faceId],
            ]);

            return true;
        } catch (AwsException $e) {
            Log::error('AWS Rekognition Delete Error: ' . $e->getMessage());
            return false;
        }
    }
}
