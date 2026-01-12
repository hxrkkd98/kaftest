<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;

/**
 * Trait for Firebase REST API operations
 * 
 * This trait provides helper methods for Firebase operations
 * that explicitly use REST API (HTTP/JSON) instead of gRPC.
 */
trait FirebaseRestTrait
{
    /**
     * Get Firestore database instance (REST API)
     * 
     * @return \Google\Cloud\Firestore\FirestoreClient
     */
    protected function getFirestoreDatabase()
    {
        try {
            $db = Firebase::firestore()->database();
            
            if (config('app.debug')) {
                Log::debug('Firestore connection established');
            }
            
            return $db;
        } catch (\Exception $e) {
            Log::error('Firestore REST API connection failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get Firebase Auth instance (REST API)
     * 
     * @return \Kreait\Firebase\Contract\Auth
     */
    protected function getFirebaseAuth()
    {
        try {
            $auth = Firebase::auth();
            
            if (config('app.debug')) {
                Log::debug('Firebase Auth connection established');
            }
            
            return $auth;
        } catch (\Exception $e) {
            Log::error('Firebase Auth REST API connection failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Execute Firestore operation with error handling
     * 
     * @param callable $operation
     * @param string $operationName
     * @return mixed
     */
    protected function executeFirestoreOperation(callable $operation, string $operationName = 'Firestore operation')
    {
        $startTime = microtime(true);
        
        try {
            $result = $operation();
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            if (config('app.debug')) {
                Log::info("$operationName completed", [
                    'duration_ms' => $duration,
                    'transport' => 'REST (HTTP/JSON)'
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error("$operationName failed", [
                'error' => $e->getMessage(),
                'duration_ms' => $duration,
                'transport' => 'REST (HTTP/JSON)',
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Execute Firebase Auth operation with error handling
     * 
     * @param callable $operation
     * @param string $operationName
     * @return mixed
     */
    protected function executeAuthOperation(callable $operation, string $operationName = 'Auth operation')
    {
        $startTime = microtime(true);
        
        try {
            $result = $operation();
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            if (config('app.debug')) {
                Log::info("$operationName completed", [
                    'duration_ms' => $duration,
                    'transport' => 'REST (HTTP/JSON)'
                ]);
            }
            
            return $result;
        } catch (\Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error("$operationName failed", [
                'error' => $e->getMessage(),
                'duration_ms' => $duration,
                'transport' => 'REST (HTTP/JSON)',
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    /**
     * Get collection documents with REST API
     * 
     * @param string $collection
     * @return array
     */
    protected function getCollectionDocuments(string $collection): array
    {
        return $this->executeFirestoreOperation(function () use ($collection) {
            $db = $this->getFirestoreDatabase();
            $documents = $db->collection($collection)->documents();
            
            $results = [];
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $results[] = array_merge(['id' => $doc->id()], $doc->data());
                }
            }
            
            return $results;
        }, "Get $collection collection");
    }

    /**
     * Get single document with REST API
     * 
     * @param string $collection
     * @param string $documentId
     * @return array|null
     */
    protected function getDocument(string $collection, string $documentId): ?array
    {
        return $this->executeFirestoreOperation(function () use ($collection, $documentId) {
            $db = $this->getFirestoreDatabase();
            $snapshot = $db->collection($collection)->document($documentId)->snapshot();
            
            if ($snapshot->exists()) {
                return array_merge(['id' => $snapshot->id()], $snapshot->data());
            }
            
            return null;
        }, "Get $collection/$documentId document");
    }

    /**
     * Create document with REST API
     * 
     * @param string $collection
     * @param array $data
     * @return string Document ID
     */
    protected function createDocument(string $collection, array $data): string
    {
        return $this->executeFirestoreOperation(function () use ($collection, $data) {
            $db = $this->getFirestoreDatabase();
            $data['created_at'] = now()->toIso8601String();
            $newRef = $db->collection($collection)->add($data);
            
            return $newRef->id();
        }, "Create $collection document");
    }

    /**
     * Update document with REST API
     * 
     * @param string $collection
     * @param string $documentId
     * @param array $data
     * @return void
     */
    protected function updateDocument(string $collection, string $documentId, array $data): void
    {
        $this->executeFirestoreOperation(function () use ($collection, $documentId, $data) {
            $db = $this->getFirestoreDatabase();
            $data['updated_at'] = now()->toIso8601String();
            $db->collection($collection)->document($documentId)->set($data, ['merge' => true]);
        }, "Update $collection/$documentId document");
    }

    /**
     * Delete document with REST API
     * 
     * @param string $collection
     * @param string $documentId
     * @return void
     */
    protected function deleteDocument(string $collection, string $documentId): void
    {
        $this->executeFirestoreOperation(function () use ($collection, $documentId) {
            $db = $this->getFirestoreDatabase();
            $db->collection($collection)->document($documentId)->delete();
        }, "Delete $collection/$documentId document");
    }
}

