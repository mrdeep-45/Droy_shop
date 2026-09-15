<?php

namespace App\Services;

use Aws\Rekognition\RekognitionClient;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\Log;

class RekognitionServiceview
{
    private $rekognitionClient;
    private $region;
    private $collectionId;

    public function __construct()
    {
        $this->region = config('services.region');
        $this->collectionId = config('services.rekognition.collection_id');

        $this->rekognitionClient = new RekognitionClient([
            'region'      => env('AWS_DEFAULT_REGION', 'ap-south-1'),
            'version'     => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    /**
     * List all faces in the collection
     */
    public function listFaces($maxResults = 100, $nextToken = null)
    {
        try {
            $params = [
                'CollectionId' => $this->collectionId,
                'MaxResults' => $maxResults,
            ];

            if ($nextToken) {
                $params['NextToken'] = $nextToken;
            }

            $result = $this->rekognitionClient->listFaces($params);

            return [
                'success' => true,
                'faces' => $result['Faces'],
                'faceModelVersion' => $result['FaceModelVersion'],
                'nextToken' => $result['NextToken'] ?? null,
                'totalFaces' => count($result['Faces'])
            ];
        } catch (AwsException $e) {
            Log::error('Rekognition ListFaces Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'faces' => []
            ];
        }
    }

    /**
     * Get collection statistics
     */
    public function getCollectionInfo()
    {
        try {
            $result = $this->rekognitionClient->describeCollection([
                'CollectionId' => $this->collectionId
            ]);

            return [
                'success' => true,
                'collectionArn' => $result['CollectionARN'],
                'faceCount' => $result['FaceCount'],
                'faceModelVersion' => $result['FaceModelVersion'],
                'creationTimestamp' => $result['CreationTimestamp']
            ];
        } catch (AwsException $e) {
            Log::error('Rekognition DescribeCollection Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Search for a specific face by Face ID
     */
    public function getFaceById($faceId)
    {
        try {
            $result = $this->rekognitionClient->listFaces([
                'CollectionId' => $this->collectionId,
                'FaceIds' => [$faceId]
            ]);

            return [
                'success' => true,
                'face' => $result['Faces'][0] ?? null
            ];
        } catch (AwsException $e) {
            Log::error('Rekognition GetFaceById Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function searchFace(string $imageBytes, float $threshold = 90.0): ?array
    {
        try {
            $result = $this->rekognitionClient->searchFacesByImage([
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
            throw new \Exception('Failed to process image for recognition. AWS Rekognition error occurred.');
        }
    }

    public function deleteFaces(array $faceIds): array
    {
        $deleted = [];
        $chunks  = array_chunk($faceIds, 4096);

        foreach ($chunks as $chunk) {
            try {
                $result = $this->rekognitionClient->deleteFaces([
                    'CollectionId' => $this->collectionId,
                    'FaceIds'      => $chunk,
                ]);

                $deleted = array_merge($deleted, $result['DeletedFaces'] ?? []);
            } catch (AwsException $e) {
                Log::error('Rekognition Bulk DeleteFaces Error: ' . $e->getMessage());
            }
        }

        return $deleted;
    }
}
